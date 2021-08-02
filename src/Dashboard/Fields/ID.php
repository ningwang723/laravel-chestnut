<?php

namespace Chestnut\Dashboard\Fields;

class ID extends Text
{
    public function __construct($label = "ID")
    {
        parent::__construct("id", $label);

        $this->hideWhenCreating()->readonly()->sortable();
    }
}
