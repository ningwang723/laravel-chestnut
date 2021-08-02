<?php
namespace Chestnut\Dashboard\Fields;

class Image extends File
{
    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label);

        $this->setAttribute("type", "image");
    }

    public function multiple()
    {
        return $this->setAttribute('multiple', true);
    }
}
