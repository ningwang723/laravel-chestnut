<?php

namespace Chestnut\Dashboard\Statistic;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

abstract class Numeric extends Statistic
{
    protected $component = "statistic-numeric";

    public function title()
    {
        return "Numeric";
    }

    public function count(Request $request, $class)
    {
        $count = $this->newQuery($request, $class)->count();

        $total = $class::count();

        return [
            "data" => $count,
            "increase_percent" => sprintf("%.2f", $count / $total * 100)
        ];
    }

    public function sum(Request $request, $class, $column, $total = null)
    {
        $defaultRanges = array_keys($this->ranges());

        $range = $request->get("range", Arr::first($defaultRanges));

        $model = new $class;

        $sum = $model->where('created_at', '>=', DB::raw("CURDATE() - FROM_DAYS({$range})"))->sum($column);

        if (empty($total)) {
            $total = $model->sum($column);
        }

        return [
            "data" => $sum,
            "increase_percent" => sprintf("%.2f", $sum / $total * 100)
        ];
    }
}
