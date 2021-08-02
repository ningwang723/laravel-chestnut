<?php

namespace Chestnut\Dashboard\Exceptions;

use Exception;

class NutCreateException extends Exception
{
    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @param  string|null  $redirectTo
     * @return void
     */
    public function __construct($message = 'Add relationship property for unsave nut.')
    {
        parent::__construct($message);
    }
}
