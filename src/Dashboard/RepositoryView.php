<?php

namespace Chestnut\Dashboard;

use Illuminate\Support\Facades\Cache;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;

class RepositoryView implements Jsonable, JsonSerializable
{
    protected $viewDefaults = ["index", "create", "detail", 'edit'];

    protected $repository;

    protected $instance;

    protected $views;

    public function __construct($repository)
    {
        $this->repository = $repository;

        $this->views =
            empty($this->repository::$views) ? $this->viewDefaults : $this->repository::$views;
    }

    public function getViews(): array
    {
        return $this->views;
    }

    public function getRepositoryName(): string
    {
        $name = explode("\\", $this->repository);

        return array_pop($name);
    }

    public function showOnMenu()
    {
        return $this->repository::$showOnMenu;
    }

    public function toArray()
    {
        $views = [];

        foreach ($this->getViews() as $view) {
            array_push($views, $this->{'make' . ucfirst($view)}());
        }

        return $views;
    }

    public function getCacheKey($view)
    {
        return "repository.{$this->repository}.{$view}";
    }

    public function makeIndex()
    {
        $repository = $this->getRepository();

        $view = [
            "name" => $repository->getName(),
            "text" => $repository->title(),
            "path" => "/" . $repository->getName(),
            "group" => $repository->group(),
            "component" => "Index",
            "fields" => $repository->getFields()->toFront(),
            "actions" => $repository->actions(),
            "row-actions" => $repository->getRowActions(),
            "cards" => $repository->cards(),
            "readonly" => $repository->readonly
        ];

        return $view;
    }

    public function makeCreate()
    {
        $repository = $this->getRepository();

        $view =  [
            "path" => "create",
            "name" => "{$repository->getName()}.create",
            "text" =>
            "新建{$repository->title()}",
            "component" => "Form",
            "fields" => $repository->getFields("create")->toFront()
        ];

        return $view;
    }

    public function makeEdit()
    {
        $repository = $this->getRepository();

        $view = [
            "path" => ":id/edit",
            "name" => "{$repository->getName()}.edit",
            "text" => "修改{$repository->title()}",
            "component" =>
            "Form",
            "fields" => $repository->getFields("edit")->toFront()
        ];

        return $view;
    }

    public function makeDetail()
    {
        $repository = $this->getRepository();

        $view = [
            "path" => ":id",
            "name" => "{$repository->getName()}.detail",
            "text" => "{$repository->title()}详情",
            "component" =>
            "Detail",
            "fields" => $repository->getFields("detail")->toFront()
        ];

        return $view;
    }

    public function getRepository()
    {
        if (isset($this->instance)) {
            return $this->instance;
        }

        $this->instance = new $this->repository();

        return $this->instance;
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
