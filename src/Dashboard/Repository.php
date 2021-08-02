<?php

namespace Chestnut\Dashboard;

use Chestnut\Dashboard\Fields\Relations\HasManyThrough;
use Chestnut\Dashboard\Fields\Relations\MorphTo;
use Chestnut\Dashboard\ORMDriver\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller;


abstract class Repository extends Controller
{
    protected $namespace = "App\Models";

    protected $ormDrivers = [];

    protected $driver;

    public static $search = [];

    public static $modeForView = [
        "edit" => 'updating',
        "create" => "creating"
    ];

    public static $views;

    protected $relationFields;

    public function __construct()
    {
        $this->boot();
    }

    public function boot()
    {
    }

    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    public function getModelName()
    {
        $name = explode("\\", get_class($this));

        return $this->namespace . '\\' . array_pop($name);
    }

    public function getName()
    {
        $name = explode("\\", get_class($this));
        $name = array_pop($name);

        return Str::plural(strtolower($name));
    }

    /**
     * @return Field[]
     */
    abstract function fields(): array;

    public function getFields($view = "index"): Collection
    {
        if (isset(static::$modeForView[$view])) {
            $view = static::$modeForView[$view];
        }

        return (new FieldCollection($this->fields()))->filter(function ($field) use ($view) {
            return $field->showOn($view);
        });
    }

    public function actions(): array
    {
        return [];
    }

    public function cards()
    {
        return [];
    }

    public function getORMDriver($driver = null): Driver
    {
        if (is_null($driver)) {
            return $this->defaultORMDriver();
        }

        if (isset($this->ormDrivers[$driver])) {
            return $this->ormDrivers[$driver];
        }

        return $this->makeDriver($driver);
    }

    public function makeDriver($driver)
    {
        $instance = new $driver($this->getModelName());

        $this->setORMDriver($driver, $instance);

        return $instance;
    }

    /**
     * Get default orm driver
     *
     * @return Chestnut\Dashboard\ORMDriver\Driver
     */
    public function defaultORMDriver(): Driver
    {
        if (isset($this->ormDrivers['default'])) {
            return $this->ormDrivers['default'];
        }

        $default = config('chestnut.dashboard.driver');

        $driver = config('chestnut.dashboard.drivers.' . $default);

        $driver = $this->makeDriver($driver);

        return $driver;
    }

    /**
     * Store driver instance
     *
     * @param string $driver
     * @param Chestnut\Dashboard\ORMDriver\Driver $driverInstance
     * @return void
     */
    public function setORMDriver($driver, $driverInstance)
    {
        $this->ormDriver[$driver] = $driverInstance;
    }

    public function newQuery($driver = null)
    {
        return $this->getORMDriver($driver)->getQuery();
    }

    public function getOptions(Request $request)
    {
        $fields = $this->getFields($request->get("view", "index"));

        if ($request->view !== "index") {
            $model = $this->newQuery();

            $fields->each(function ($relation) use ($model) {
                if (method_exists($relation, 'getOptions')) {
                    $relation->getOptions($model);
                }
            });

            return ["errno" => 0, "data" => $fields->toFront()];
        }

        $actions = $this->actions();
        $cards = $this->cards();

        return ["errno" => 0, "data" => [
            "fields" => [
                "index" => $fields->toFront()
            ],
            "actions" => $actions,
            "cards" => $cards
        ]];
    }

    public function index(Request $request)
    {
        $fields = $this->getFields();

        $relations = $fields->getRelationFields();

        $query = $this->newQuery($this->driver)->select($fields->getProperties());

        $query = $this->processRelations($relations, $query);

        $query = $this->searchRepository($request, $query);
        $query = $this->sortRepository($request, $query, $fields->filter(function ($field) {
            return $field->hasAttribute('sortable');
        }));

        $perPage = $request->get('per_page', 10);

        $model = $query
            ->paginate($perPage)
            ->withPath($this->getName())
            ->onEachSide(1)->withQueryString();

        $model->makeHidden($relations->getHiddens());

        return [
            "errno" => 0,
            "data" => $model
        ];
    }

    public function create(Request $request)
    {
        $fields = $this->getFields("creating");

        return $this->toResponse($request, null, $fields);
    }

    public function edit(Request $request, $id)
    {
        $fields = $this->getFields("updating");

        $relations = $fields->getRelationFields();

        $query = $this->newQuery()->select($fields->getProperties());

        $query = $this->processRelations($relations, $query);

        $model = $query->find($id);

        $model->makeHidden($relations->getHiddens());

        return $this->toResponse($request, $model);
    }

    public function update(Request $request, $id)
    {
        $model = $this->newQuery()->find($id);

        $this->applyAttributesToModel("edit", $request, $model);

        $model->save();

        return ['errno' => 0, 'message' => "保存成功"];
    }

    public function store(Request $request)
    {
        $model = $this->newQuery();

        $this->applyAttributesToModel("create", $request, $model);

        $model->publisher()->associate(auth("chestnut")->user());

        $model->save();

        return ['errno' => 0, 'message' => "保存成功"];
    }

    public function detail(Request $request, $id)
    {
        $fields = $this->getFields("detail");

        $relations = $fields->getRelationFields();

        $query = $this->newQuery()->select($fields->getProperties());

        $query = $this->processRelations($relations, $query);

        $model = $query->find($id);

        $model->makeHidden($relations->getHiddens());

        return $this->toResponse($request, $model);
    }

    public function processRelations($relations, $query)
    {
        $query = $query->addSelect($relations->reduce(function ($acc, $field) {
            if ($field instanceof HasManyThrough) {
                return $acc;
            }

            array_push($acc, $field->prop);

            if ($field instanceof MorphTo) {
                array_push($acc, $field->morphType());
            }

            return $acc;
        }, []));

        $requestRelations = [];

        foreach ($relations as $relation) {
            $requestRelations[$relation->relation] =  function ($query) use ($relation) {
                $query->select("id", $relation->title());
            };
        }

        return $query->with($requestRelations);
    }

    public function searchRepository(Request $request, $query)
    {
        if ($request->has("keyword")) {
            $keyword = $request->keyword;

            foreach (static::$search as $search) {
                $query = $query->orWhere($search, 'like', "%{$keyword}%");
            }
        }

        return $query;
    }

    public function sortRepository(Request $request, $query, $sortable)
    {
        if ($request->has('sort')) {
            $sorts = $request->sort;
            $sorts = explode(",", $sorts);

            foreach ($sorts as $sort) {
                $sort = explode("|", $sort);

                $colomn = $sort[0];
                $order = isset($sort[1]) ? $sort[1] : 'desc';

                $query = $query->orderBy($colomn, $order);
            }
        } else {
            $query = $query->orderBy("created_at", $request->get('created_at', 'desc'));
        }

        return $query;
    }

    public function applyAttributesToModel($view, $request, $model)
    {
        $fields = $this->getFields($view)->filter(function ($field) {
            return !$field->isReadonly();
        });

        $fields->each(function ($field) use ($request, $model) {
            $field->fillAttributeFromRequest($request, $model);
        });
    }

    public function destroy(Request $request, $id)
    {
        $model = $this->newQuery()->find($id);

        $model->delete();

        return ['errno' => 0, 'message' => "删除成功"];
    }

    public function doAction(Request $request)
    {
        $action = new $request->action();

        $response = $action->handle($request, $this);

        return $response;
    }

    public function calculateStatistic(Request $request)
    {
        $statistic = new $request->statistic;

        $data = $statistic->calculate($request);

        return [
            'errno' => 0,
            'data' => $data
        ];
    }

    public function toResponse(Request $request, $model = null, $fields = null)
    {
        $response = ['errno' => 0, 'data' => $model];

        if (!empty($fields)) {
            $response['fields'] = array_values($fields);
        }

        return $response;
    }
}
