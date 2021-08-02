<?php

namespace Chestnut\Dashboard;

use Chestnut\Dashboard\Fields\Relations\MorphTo;
use Chestnut\Dashboard\Fields\Relations\RelationField;
use Illuminate\Support\Collection;

class FieldCollection extends Collection
{
    public function getRelationFields()
    {
        return $this->filter(function ($field) {
            return $field instanceof RelationField;
        });
    }

    public function getProperties(): array
    {
        return array_filter($this->pluck("prop")->all());
    }

    public function toFront()
    {
        return array_values($this->all());
    }

    public function getHiddens()
    {
        return $this->reduce(function ($acc, $field) {
            if (!method_exists($field, 'hiddenProperties')) {
                return $acc;
            }

            return array_merge($acc, $field->hiddenProperties());
        }, []);
    }
}
