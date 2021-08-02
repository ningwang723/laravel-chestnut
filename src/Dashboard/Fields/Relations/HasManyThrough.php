<?php

namespace Chestnut\Dashboard\Fields\Relations;

class HasManyThrough extends RelationField
{
    public function __construct(string $relation, string $label)
    {
        parent::__construct($relation, $label);

        $this->multiple();
    }

    public function relationKey()
    {
        return null;
    }

    public function key()
    {
        return null;
    }

    public function prepareQuery($query)
    {
        return $query->select("id")->first()->{$this->relation}()->getRelated()
            ->select('id', $this->title());
    }
}
