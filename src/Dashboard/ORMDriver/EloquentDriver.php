<?php

namespace Chestnut\Dashboard\ORMDriver;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EloquentDriver extends Driver
{
    /**
     * Relations for model
     *
     * @var array
     */
    public $with;

    /**
     * Generate Eloquent query
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        $query = parent::getQuery();

        if (isset($this->with)) {
            $query = $query->with($this->with);
        }

        if ($this->isSoftDelete()) {
            $query = $query->withTrashed();
        }

        return $query;
    }

    /**
     * Determine model has softdelete
     *
     * @return boolean
     */
    public function isSoftDelete(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->getModel()));
    }

    /**
     * Find model by id
     *
     * @param integer $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($id): Model
    {
        return $this->getQuery()->find($id);
    }
}
