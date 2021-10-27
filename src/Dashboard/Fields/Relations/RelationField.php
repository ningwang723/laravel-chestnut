<?php

namespace Chestnut\Dashboard\Fields\Relations;

use Chestnut\Dashboard\Fields\Select;

abstract class RelationField extends Select
{
    public $relation;

    protected $perPage = 5;

    public function __construct(string $prop, string $label)
    {
        $this->relation = $prop;

        parent::__construct($this->relationKey(), $label);
    }

    public function perPage($perPage)
    {
        $this->perPage = $perPage;
    }

    public function getOptions($query)
    {
        $query = $this->prepareQuery($query);

        $options = $query->get();

        $this->options($options);
    }

    public function prepareQuery($query)
    {
        $query = $query->addSelect($this->relationKey());

        $model = $query->first()->{$this->relation}()->getRelated();

        return $model
            ->select($this->key(), $this->title());
    }

    public function options($options, $label_prop = "name", $value_prop = "id")
    {
        $this->setAttribute("options", $options);
    }

    public function relationKey()
    {
        return "{$this->relation}_{$this->key()}";
    }

    public function title()
    {
        return "name";
    }

    public function key()
    {
        return "id";
    }

    public function hiddenProperties()
    {
        return $this->prop ? [$this->prop] : [];
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['relation'] = $this->relation;
        $data['name'] = $this->title();
        $data['key'] = $this->key();

        return $data;
    }
}
