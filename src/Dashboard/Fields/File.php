<?php

namespace Chestnut\Dashboard\Fields;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;

class File extends Field
{
    public $component = "file-uploader";

    public function __construct($prop, $label)
    {
        parent::__construct($prop, $label);

        $this->hideFromIndex();
    }

    public function fillAttributeFromRequest(Request $request, $model)
    {
        if ($request->exists($this->getProperty())) {
            $value = $request[$this->getProperty()];

            if ($value instanceof UploadedFile) {
                $value = URL::to($value->store(config("chestnut.dashboard.upload_storage")));
            }

            $model->{$this->getProperty()} = $value;
        }
    }
}
