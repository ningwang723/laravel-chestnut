<?php

namespace Chestnut\Dashboard\Fields;

class Editor extends Field
{
    public $component = "editor";

    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label, "Editor");

        $this->hideFromIndex();
    }
}
