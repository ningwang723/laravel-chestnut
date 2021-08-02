<?php

namespace Chestnut\Dashboard\Fields;

class SoftDelete extends Datetime
{
    public function __construct($label)
    {
        parent::__construct("deleted_at", $label);

        $this->onlyOnIndex()->sortable();
    }
}
