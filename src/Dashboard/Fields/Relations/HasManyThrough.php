<?php

namespace Chestnut\Dashboard\Fields\Relations;

use Illuminate\Support\Str;

class HasManyThrough extends RelationField
{
    public function __construct(string $model, string $label)
    {
        parent::__construct($model, $label);

        $this->multiple();
    }

    public function getRelation()
    {
        $relation = parent::getRelation();

        return Str::plural($relation);
    }

    public function relationKey()
    {
        return null;
    }

    public function key()
    {
        return null;
    }
}
