<?php

namespace Chestnut\Dashboard\Fields;

class Password extends Text
{
    public function __construct($prop, $label = 'Password')
    {
        parent::__construct($prop, $label);

        $this->setAttribute('type', 'password');
        $this->onlyOnForms();
    }
}
