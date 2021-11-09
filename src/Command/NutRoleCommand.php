<?php

namespace Chestnut\Command;

use Illuminate\Console\Command;

class NutRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nut:role {role : 角色名称}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a nut role';

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
        $role = $this->argument('role');

        $this->call("permission:create-role", ["name" => $role, "guard" => "chestnut"]);
        return 0;
    }
}
