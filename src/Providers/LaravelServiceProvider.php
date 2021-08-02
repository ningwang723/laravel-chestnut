<?php

namespace Chestnut\Providers;

use Chestnut\Auth\Nuts\Manager;
use Chestnut\Auth\Nuts\Permission;
use Chestnut\Auth\Nuts\Role;
use Chestnut\Dashboard\Uploader\Uploader;
use Illuminate\Support\Facades\Gate;

class LaravelServiceProvider extends AbstractServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/chestnut.php' => config_path('chestnut.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/config/auth.php' => config_path('auth.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/assets' => public_path('/'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/translates' => resource_path('lang/vendor/chestnut'),
        ], 'translate');

        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        // $this->loadViewsFrom(__DIR__ . '/views/', 'chestnut');
        // $this->loadTranslationsFrom(__DIR__ . '/translates', 'chestnut');
        $this->loadMigrationsFrom(__DIR__ . "/migrations");

        if (!app()->runningInConsole()) {
            app("shell")->registerRepositories($this->app->config->get('chestnut.dashboard.nutsIn'), "app");

            if (config('chestnut.auth.rbac', false)) {
                app("shell")->registerRepositories(__DIR__ . "/../Auth/Nuts", "rbac");
            }
        }
    }
}
