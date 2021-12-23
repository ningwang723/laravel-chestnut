<?php

namespace Chestnut\Auth\Nuts;

use Chestnut\Auth\Models\Permission as Permission;
use Chestnut\Dashboard\Nut;
use Chestnut\Facades\Shell;

class Role extends Nut
{
    //
    protected $namespace = 'Chestnut\Auth\Models';

    protected $except = ['permissions'];

    protected $with = ['permissions:id,name'];

    public function fields(): array
    {
        return [
            Shell::ID(),
            Shell::Text("name", "名称"),
            Shell::HasManyThrough(Permission::class, "permissions", "权限")->hideFromIndex(),
        ];
    }

    public function title()
    {
        return "角色";
    }

    public function group()
    {
        return "权限管理";
    }
}
