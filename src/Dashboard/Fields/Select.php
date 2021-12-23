<?php

namespace Chestnut\Dashboard\Fields;

use Illuminate\Support\Collection;

class Select extends Field
{
    public $component = "select";

    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label);
    }

    public function options($options, $label_prop = "name", $value_prop = "id")
    {
        if ($options instanceof Collection) {
            $options = $options->pluck($label_prop, $value_prop);
        }

        $this->setAttribute("label", $label_prop);
        $this->setAttribute("value", $value_prop);

        return $this->setAttribute('options', $options);
    }

    public function multiple()
    {
        return $this->setAttribute('multiple', true);
    }

    public function prop(string $prop)
    {
        return $this->setAttribute('prop', $prop);
    }

    public function lazyLoad(string $api)
    {
        return $this->setAttribute('lazyLoadApi', $api);
    }
}
