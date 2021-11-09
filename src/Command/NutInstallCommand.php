<?php

namespace Chestnut\Command;

use Illuminate\Console\Command;

class NutInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nut:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install nut';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);
        $this->call('vendor:publish', ['--provider' => 'Chestnut\ChestnutServiceProvider']);
        $this->call('storage:link');
        $this->call('migrate');
        $this->call('nut:role', ['role' => 'Chestnut Manager']);
        $this->call('nut:manager');
        return 0;
    }
}
