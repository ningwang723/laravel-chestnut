<?php

namespace Chestnut\Dashboard\Fields;

class CreatedAt extends Datetime
{
    public function __construct($label = "Created At")
    {
        parent::__construct('created_at', $label);

        $this->onlyOnIndex()->sortable();
    }
}
