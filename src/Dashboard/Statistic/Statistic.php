<?php

namespace Chestnut\Dashboard\Statistic;

use Carbon\Carbon;
use Illuminate\Http\Request;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

abstract class Statistic implements Jsonable, JsonSerializable
{
    protected $result;

    abstract function calculate(Request $request);

    abstract function ranges();

    abstract function title();

    public function getRanges($request)
    {
        $defaultRanges = array_keys($this->ranges());
        $range = $request->range;

        if (!array_search($range, $defaultRanges)) {
            return Arr::first($defaultRanges);
        }

        return $range;
    }

    public function newQuery($class)
    {
        $model = new $class;

        return $model;
    }

    public function getDates($range)
    {
        $end = Carbon::now('Asia/Shanghai');
        $start = Carbon::now('Asia/Shanghai')->subDays($range);

        return [$start, $end];
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            "statistic" => static::class,
            "component" => $this->component,
            "ranges" => $this->ranges(),
            "title" => $this->title()
        ];
    }
}
