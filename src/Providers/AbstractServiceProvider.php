<?php

namespace Chestnut\Providers;

use Chestnut\Dashboard\Shell;
use Illuminate\Support\ServiceProvider;

abstract class AbstractServiceProvider extends ServiceProvider
{
    protected $commands = [
        \Chestnut\Command\NutMakeCommand::class,
        \Chestnut\Command\NutInstallCommand::class,
        \Chestnut\Command\NutManagerCommand::class,
        \Chestnut\Command\NutRoleCommand::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('shell', Shell::class);
        $this->mergeConfigFrom(__DIR__ . "/../config/chestnut.php", "chestnut");

        $this->registerCommands();
    }

    public function registerCommands()
    {
        if (app()->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    abstract public function boot();
}
