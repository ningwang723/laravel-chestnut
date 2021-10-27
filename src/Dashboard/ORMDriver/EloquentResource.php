<?php

namespace Chestnut\Dashboard\ORMDriver;

use Chestnut\Contracts\Dashboard\Resource as ResourceContract;
use Chestnut\Dashboard\Fields\Relations\HasMany;
use Chestnut\Dashboard\Fields\Relations\HasManyThrough;
use Chestnut\Dashboard\Fields\Relations\MorphTo;
use Chestnut\Dashboard\GetResourceNames;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EloquentResource implements ResourceContract
{
    public function __construct($nut)
    {
        $this->nut = $nut;
    }

    public function newQuery()
    {
        $model = $this->nut->getModelName();

        return new $model();
    }


    public function getOption($key)
    {
    }

    public function getFields($view = 'index')
    {
        return $this->nut->getFields($view);
    }

    public function index(Request $request)
    {
        $fields = $this->getFields();

        $relations = $fields->getRelationFields();

        $query = $this->newQuery()->select($fields->getProperties());

        $query = $this->processRelations($relations, $query);

        $query = $this->searchRepository($request, $query);
        $query = $this->sortRepository($request, $query, $fields->filter(function ($field) {
            return $field->hasAttribute('sortable');
        }));

        $perPage = $request->get('per_page', 10);

        $model = $query
            ->paginate($perPage)
            ->withPath($this->nut->getName())
            ->onEachSide(1)->withQueryString();

        $model->makeHidden($relations->getHiddens());

        return $model;
    }

    public function detail($id)
    {
        $fields = $this->getFields("detail");

        $relations = $fields->getRelationFields();

        $query = $this->newQuery()->select($fields->getProperties());

        $query = $this->processRelations($relations, $query);

        $model = $query->find($id);

        $model->makeHidden($relations->getHiddens());

        return $model;
    }

    public function edit($id)
    {
        $fields = $this->getFields("updating");

        $relations = $fields->getRelationFields();

        $query = $this->newQuery()->select($fields->getProperties());

        $query = $this->processRelations($relations, $query);

        $model = $query->find($id);

        $model->makeHidden($relations->getHiddens());

        return $model;
    }

    public function store(Request $request)
    {
        $model = $this->newQuery();

        $this->applyAttributesToModel("create", $request, $model);

        $model->publisher()->associate(auth("chestnut")->user());

        return $model->save();
    }

    public function update(Request $request, $id)
    {
        $model = $this->newQuery()->find($id);

        $this->applyAttributesToModel("edit", $request, $model);

        return $model->save();
    }

    public function destroy($id)
    {
        $model = $this->newQuery()->find($id);

        return $model->delete();
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

            foreach ($this->nut::$search as $search) {
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
}
