<?php

namespace Chestnut\Dashboard;

abstract class Action
{
    public static function make($title, $options = null)
    {
        return [
            "title" => $title,
            "options" => $options,
            "action" => static::class
        ];
    }
}
