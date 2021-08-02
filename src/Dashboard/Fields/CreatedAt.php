<?php

namespace Chestnut\Dashboard\Fields;

class CreatedAt extends Datetime
{
    public function __construct($label)
    {
        parent::__construct('created_at', $label);

        $this->onlyOnIndex()->sortable();
    }
}
