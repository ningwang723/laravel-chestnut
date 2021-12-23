<?php

namespace Chestnut\Dashboard\Fields\Relations;

use Chestnut\Dashboard\Fields\Select;
use Illuminate\Support\Facades\Cache;

abstract class RelationField extends Select
{
    /**
     * Relation model
     *
     * @var string
     */
    protected $model;

    /**
     * Perpage in options
     *
     * @var int
     */
    protected $perPage = 5;

    /**
     * Relation Field Constructor
     *
     * @param string $model Relation model name
     */
    public function __construct(string $model, string $label)
    {
        $this->model = $model;

        parent::__construct($this->relationKey(), $label);
    }

    public function getProperty()
    {
        return $this->getRelation() . '_' . $this->key();
    }

    /**
     * Get relation model base name
     *
     * @return string
     */
    public function getModelName()
    {
        $name = explode("\\", $this->model);
        $name = array_pop($name);

        return strtolower($name);
    }

    /**
     * Get relation name
     *
     * @return string
     */
    public function getRelation()
    {
        return $this->getModelName();
    }

    /**
     * Set perpage in options
     *
     * @return void
     */
    public function perPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * Get options
     *
     * @return mixed[]
     */
    public function getOptions()
    {
        $model = new $this->model();

        return $model->select($model->getKeyName(), $this->title())->cursorPaginate($this->perPage)->withPath("options");
    }

    public function options($options, $label_prop = "name", $value_prop = "id")
    {
        parent::options($options, $label_prop, $value_prop);
    }

    /**
     * Get field relation key
     *
     * @return string
     */
    public function relationKey()
    {
        $name = $this->getModelName();

        return "{$name}_{$this->key()}";
    }

    /**
     * Get field relation title attribute
     *
     * @return string
     */
    public function title()
    {
        return "name";
    }

    /**
     * Get field relation primary key
     */
    public function key()
    {
        if ($key = Cache::get($this->model . "_primary")) {
            return $key;
        }

        $key =  (new $this->model)->getKeyName();

        Cache::forever($this->model . "_primary", $key);
        return $key;
    }

    /**
     * Get relation field hidden properties
     */
    public function hiddenProperties()
    {
        return $this->prop ? [$this->prop] : [];
    }

    public function jsonSerialize()
    {
        $options = $this->getOptions();
        $this->options($options);

        $data = parent::jsonSerialize();

        $data['relation'] = $this->getRelation();
        $data['name'] = $this->title();
        $data['key'] = $this->key();

        return $data;
    }
}
