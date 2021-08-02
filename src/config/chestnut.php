<?php

return [
    /**
                         * Chestnut Auth config
                 */
    'auth'      => [

        /**
         * Api token expire time.
         */
        'expire' => 1,

        /**
         * Enable Role-base access control
         */
        'rbac'   => true,
    ],

    /**
     * Chestnut Dashboard config
     */
    'dashboard' => [

        /**
         * Nuts directory
         */
        'nutsIn'         => app()->basePath('app') . env('CHESTNUT_NUTS_DIRECTORY', '/Nuts'),

        /**
         * Nut ORM Drivers
         */
        'drivers'        => [
            'eloquent' => Chestnut\Dashboard\ORMDriver\EloquentDriver::class,
        ],

        /**
         * Nut ORM default driver
         */
        'driver'         => 'eloquent',

        'upload_storage' => 'upload',
    ],
];
