<?php

namespace Chestnut\Auth\Nuts;

use Chestnut\Auth\Actions\GeneratePermissionsAction;
use Chestnut\Dashboard\Nut;
use Chestnut\Facades\Shell;

class Permission extends Nut
{
    //
    protected $namespace = 'Chestnut\Auth\Models';

    public function fields(): array
    {
        return [
            Shell::ID(),
            Shell::Text('name', "名称"),
        ];
    }

    public function actions(): array
    {
        return [
            GeneratePermissionsAction::make("初始化权限")
        ];
    }

    public function title()
    {
        return "权限";
    }

    public function group()
    {
        return "权限管理";
    }
}
