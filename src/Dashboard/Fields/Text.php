<?php

namespace Chestnut\Dashboard\Fields;

class Text extends Field
{
    public $component = 'input';

    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label);
    }
}
