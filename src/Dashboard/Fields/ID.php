<?php

namespace Chestnut\Dashboard\Fields;

class ID extends Text
{
    public function __construct($prop = "id", $label = "ID")
    {
        parent::__construct($prop, $label);

        $this->setAttribute("primary", true)->hideWhenCreating()->readonly()->sortable();
    }
}
