<?php

namespace Chestnut\Dashboard\Fields;

class Datetime extends Field
{
    public $component = "date-picker";

    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label);

        $this->setAttribute('type', 'datetime');
    }
}
