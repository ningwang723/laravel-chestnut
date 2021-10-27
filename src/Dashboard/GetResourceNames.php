<?php

namespace Chestnut\Dashboard;

use Illuminate\Support\Str;

trait GetResourceNames
{
    public function getName()
    {
        $name = explode("\\", get_class($this));
        $name = array_pop($name);

        return Str::plural(strtolower($name));
    }

    public function getModelName()
    {
        $name = explode("\\", get_class($this));

        return $this->namespace . '\\' . array_pop($name);
    }
}
