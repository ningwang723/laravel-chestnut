<?php

namespace Chestnut\Dashboard\Statistic;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class Line extends Statistic
{
    protected $component = "statistic-line";

    protected $options = [];

    public function title()
    {
        return "Line";
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function withOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function toResponse($data)
    {
        $datasets = [
            [
                "name" => $this->title(),
                // "backgroundColor" => "rgba(254,120,57,.4)",
                // "borderColor" => "#fe7839",
                "data" => $data,
                // "fill" => true,
            ]
        ];

        return $datasets;
    }

    public function countByDays(Request $request, $class)
    {
        $count = $this->byDays($request, $class);

        return $this->toResponse($count);
    }

    public function countByMonths(Request $request, $class)
    {
        $count = $this->byMonths($request, $class);

        return $this->toResponse($count);
    }

    protected function convertData($data, $period)
    {
        $statistic = [];

        foreach ($period as $day) {
            $count = $data->where("date", $day->toDateString())->first();
            array_push($statistic, [$day->toDateString(),  $count ? $count->count : 0]);
        }

        return $statistic;
    }

    public function byDays(Request $request, $class)
    {
        $range = $this->getRanges($request);

        [$start, $end] = $this->getDates($range);

        $query = $this->newQuery($class);

        $counts = $query
            ->selectRaw("DATE_FORMAT(`created_at`, '%Y-%m-%d') as date")
            ->addSelect(DB::raw("count(id) as count"))
            ->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy("date")
            ->orderBy("date")->get();

        $period = $start->toPeriod($end->addDays(1), '1 day')->excludeStartDate();

        $statistic = $this->convertData($counts, $period);

        return $statistic;
    }

    public function byMonths(Request $request, $class)
    {
        $range = $this->getRanges($request);

        $end = Carbon::now()->toDateString("Y-m-d");
        $start = Carbon::now()->subMonth($range)->toDateString();

        $query = $this->newQuery($class);

        $counts = $query
            ->selectRaw("DATE_FORMAT(`created_at`, '%Y-%m-%d') as date")
            ->addSelect(DB::raw("count(id) as count"))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy("date")
            ->orderBy("date")->get();

        $period = new CarbonPeriod($start, "{$range} days", $end);

        $statistic = $this->convertData($counts, $period);

        return $statistic;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $data['options'] = $this->getOptions();

        return $data;
    }
}
