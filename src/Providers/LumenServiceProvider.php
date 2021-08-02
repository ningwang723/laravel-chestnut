<?php

namespace Chestnut\Providers;

use Chestnut\Dashboard\Uploader\Uploader;
use Illuminate\Support\Facades\Gate;

class LumenServiceProvider extends AbstractServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadMigrationsFrom(__DIR__ . "/../migrations");

        if (!app()->runningInConsole()) {
            app("shell")->registerRepositories($this->app->config->get('chestnut.dashboard.nutsIn'), "app");

            if (config('chestnut.auth.rbac', false)) {
                app("shell")->registerRepositories(__DIR__ . "/../Auth/Nuts", "rbac");
            }
        }
    }
}
