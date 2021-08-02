<?php

namespace Chestnut\Dashboard\ORMDriver;

abstract class Driver
{
    /**
     * Model name
     *
     * @var string
     */
    public $model;

    /**
     * Driver Constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->model = $name;
    }

    /**
     * Set relations for driver
     *
     * @param array $with
     * @return void
     */
    public function setWith(array $with)
    {
        $this->with = $with;
    }

    /**
     * Get Resource Model
     * Default namespace is App
     *
     * Set namespace by define $namespace in class parameter
     *
     * @return Model
     */
    public function getModel()
    {
        $model = new $this->model();

        return $model;
    }

    public function getQuery()
    {
        $query = $this->getModel();

        return $query;
    }

    /**
     * Determine model has softdelete
     *
     * @return boolean
     */
    public function isSoftDelete(): bool
    {
        return false;
    }
}
