<?php

namespace Chestnut\Dashboard\Fields\Relations;

class MorphTo extends RelationField
{
    protected $morph;

    /**
     * @param string $morph morph relation name
     */
    public function __construct(string $model, string $morph, string $label)
    {
        $this->morph = $morph;

        parent::__construct($model, $label);
    }

    public function getRelation()
    {
        return $this->morph;
    }

    /**
     * Get morph relation type
     *
     * @return string
     */
    public function morphType()
    {
        return $this->morph . "_type";
    }

    public function relationKey()
    {
        return $this->morph . "_" . $this->key();
    }

    public function hiddenProperties()
    {
        $properties = parent::hiddenProperties();

        array_push($properties, $this->morphType());

        return $properties;
    }
}
