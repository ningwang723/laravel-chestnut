<?php

namespace Chestnut\Command;

use Chestnut\Auth\Models\Manager;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class NutManagerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nut:manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a nut admin user';

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
        $this->info("Create nut admin user.");

        $data = $this->askAccount();

        while (empty($password)) {
            $password = $this->secret("Enter password");

            if (!preg_match("/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,20}/", $password)) {
                $this->error("The password you enter is not valid.");
                $this->error("Your password must include one of word, digit and !@#$%^&*.");

                $password = null;
            }
        }

        $data['password'] = $password;

        while (empty($name)) {
            $name = $this->ask("Enter user's name");

            if (empty($name)) {
                $this->error("Please enter user's name");
                $name = null;
            }
        }

        $data['name'] = $name;

        $roles = Role::all()->pluck("name")->toArray();

        $role = $this->choice("Select role to assign", $roles, 0);

        $user = Manager::create($data);
        $user->assignRole($role);

        $this->info("Create user successed.");

        return 0;
    }

    public function askAccount()
    {
        $type = $this->choice("Please select account type", ['Email', 'Phone number'], 1);

        if ($type == 'Email') {
            while (empty($email)) {
                $email = $this->ask("Enter email");

                if (!preg_match("/^[^\s@]+@[^\s@]+\.[^\s@]+$/", $email)) {
                    $this->error("The email you enter is not valid");

                    $email = null;
                }
            }

            return ["email" => $email];
        }

        while (empty($phone)) {
            $phone = $this->ask("Enter phone number");

            if (!preg_match("/^1[3-9][0-9]{9}$/", $phone)) {
                $this->error("The phone number you enter is not valid");

                $phone = null;
            }
        }

        return ['phone' => $phone];
    }
}
