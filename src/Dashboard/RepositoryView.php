<?php

namespace Chestnut\Dashboard;

use Illuminate\Support\Facades\Cache;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;

class RepositoryView implements Jsonable, JsonSerializable
{
    protected $viewDefaults = ["index", "create", "detail", 'edit'];

    protected $repository;

    protected $changed;

    protected $instance;

    protected $views;

    public function __construct($repository, $changed)
    {
        $this->repository = $repository;
        $this->changed = $changed;

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
            if (auth("chestnut")->user()->can("{$this->repository} {ucfirst($view)}")) {
                array_push($views, $this->{'make' . ucfirst($view)}());
            }
        }

        return $views;
    }

    public function getCacheKey($view)
    {
        return "repository.{$this->repository}.{$view}";
    }

    public function makeIndex()
    {
        if (!$this->changed && Cache::has($this->getCacheKey("index"))) {
            return Cache::get($this->getCacheKey("index"));
        }

        $repository = $this->getRepository();

        $view = [
            "name" => $repository->getName(),
            "text" => $repository->title(),
            "path" => "/" . $repository->getName(),
            "group" => $repository->group(),
            "component" => "Index"
        ];

        Cache::forever($this->getCacheKey("index"), $view);

        return $view;
    }

    public function makeCreate()
    {
        if (!$this->changed && Cache::has($this->getCacheKey("create"))) {
            return Cache::get($this->getCacheKey("create"));
        }

        $repository = $this->getRepository();

        $view =  [
            "path" => "create",
            "name" => "{$repository->getName()}.create",
            "text" =>
            "新建{$repository->title()}",
            "component" => "Form"
        ];

        Cache::forever($this->getCacheKey("create"), $view);

        return $view;
    }

    public function makeEdit()
    {
        if (!$this->changed && Cache::has($this->getCacheKey("edit"))) {
            return Cache::get($this->getCacheKey("edit"));
        }

        $repository = $this->getRepository();

        $view = [
            "path" => ":id/edit",
            "name" => "{$repository->getName()}.edit",
            "text" => "修改{$repository->title()}",
            "component" => "Form"
        ];

        Cache::forever($this->getCacheKey("edit"), $view);

        return $view;
    }

    public function makeDetail()
    {
        if (!$this->changed && Cache::has($this->getCacheKey("detail"))) {
            return Cache::get($this->getCacheKey("detail"));
        }

        $repository = $this->getRepository();

        $view = [
            "path" => ":id",
            "name" => "{$repository->getName()}.detail",
            "text" => "{$repository->title()}详情",
            "component" => "Detail"
        ];

        Cache::forever($this->getCacheKey("detail"), $view);

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
