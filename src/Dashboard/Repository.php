<?php

namespace Chestnut\Dashboard;

use Chestnut\Dashboard\Fields\Relations\HasManyThrough;
use Chestnut\Dashboard\Fields\Relations\MorphTo;
use Chestnut\Dashboard\ORMDriver\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;


abstract class Repository extends Controller
{
    use GetResourceNames;

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

    /**
     * @return Field[]
     */
    abstract function fields(): array;



    public function actions(): array
    {
        return [];
    }

    public function cards()
    {
        return [];
    }

    public function rowActions()
    {
        return [];
    }

    public function defaultRowActions()
    {
        return ['detail', 'edit', 'delete'];
    }

    public function getRowActions()
    {
        return array_merge($this->rowActions(), $this->defaultRowActions());
    }

    public function getORMDriver($driver = null): Driver
    {
        if (is_null($driver)) {
            return $this->defaultORMDriver();
        }

        if (isset($this->ormDrivers[$driver])) {
            return $this->ormDrivers[$driver];
        }

        $driver = config("chestnut.dashboard.drivers." . $driver);

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

    public function getResource()
    {
        return $this->getORMDriver($this->driver)->getResource($this);
    }

    public function getFields($view = "index"): Collection
    {
        if (isset(static::$modeForView[$view])) {
            $view = static::$modeForView[$view];
        }

        return (new FieldCollection($this->fields()))->filter(function ($field) use ($view) {
            return $field->showOn($view);
        });
    }

    public function index(Request $request)
    {
        $resource = $this->getResource();

        return [
            "errno" => 0,
            "data" => $resource->index($request)
        ];
    }

    public function create(Request $request)
    {
        // return $this->toResponse($request, null, $fields);
    }

    public function edit(Request $request, $id)
    {
        $resource = $this->getResource();

        return $this->toResponse($request, $resource->edit($id));
    }

    public function update(Request $request, $id)
    {
        $resource = $this->getResource();

        $resource->update($request, $id);

        return ['errno' => 0, 'message' => "保存成功"];
    }

    public function store(Request $request)
    {
        $resource = $this->getResource();

        $resource->store($request);

        return ['errno' => 0, 'message' => "保存成功"];
    }

    public function detail(Request $request, $id)
    {
        $resource = $this->getResource();

        return [
            "errno" => 0,
            "data" => $resource->detail($id)
        ];
    }



    public function destroy(Request $request, $id)
    {
        $resource = $this->getResource();
        $resource->destroy($id);

        return ['errno' => 0, 'message' => "删除成功"];
    }

    public function doAction(Request $request)
    {
        $action = new $request->action();

        $response = $action->handle($request, $this);

        return $response;
    }

    public function toResponse(Request $request, $model = null, $fields = null)
    {
        $response = ['errno' => 0, 'data' => $model];

        return $response;
    }
}
