<?php

namespace Chestnut\Dashboard\Fields;

class Avatar extends File
{
    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label);

        $this->setAttribute("type", "avatar");
    }
}
