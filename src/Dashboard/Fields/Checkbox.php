<?php

namespace Chestnut\Dashboard\Fields;

class Checkbox extends Field
{
    public $component = 'checkbox';

    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label, "Checkbox");
    }

    public function options($options)
    {
        return $this->setAttribute('options', $options);
    }

    public function prop($prop)
    {
        return $this->setAttribute('prop', $prop);
    }
}
