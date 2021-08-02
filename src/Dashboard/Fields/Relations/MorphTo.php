<?php

namespace Chestnut\Dashboard\Fields\Relations;

class MorphTo extends RelationField
{
    public function morphType()
    {
        return $this->relation . "_type";
    }

    public function prepareQuery($query)
    {
        $query = $query->addSelect($this->morphType());

        return parent::prepareQuery($query);
    }

    public function hiddenProperties()
    {
        $properties = parent::hiddenProperties();

        array_push($properties, $this->morphType());

        return $properties;
    }
}
