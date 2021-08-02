<?php

namespace Chestnut\Dashboard;

use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Support\Str;

/**
 * Laravel admin resource
 *
 * @author Leon Zhang <33543015@qq.com>
 */
abstract class Nut extends Repository
{
    /**
     * Display nut in menu
     *
     * @var boolean
     */
    public static $showOnMenu = true;

    public function title()
    {
        return $this->getName();
    }

    /**
     * Nut's Group
     *
     * @return void
     */
    public function group()
    {
        return null;
    }
}
