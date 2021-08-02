<?php

namespace Chestnut\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Chestnut\Dashboard\Fields\Text Text($prop, $label)
 * @method static \Chestnut\Dashboard\Fields\Datetime Datetime($prop, $label)
 * @method static \Chestnut\Dashboard\Fields\Select Select($prop, $label)
 * @method static \Chestnut\Dashboard\Fields\TextArea TextArea($prop, $label)
 *
 * @see \Chestnut\Dashboard
 */
class Shell extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'shell';
    }
}
