<?php

namespace Chestnut\Auth\Nuts;

use Chestnut\Dashboard\Nut;
use Chestnut\Facades\Shell;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class Manager extends Nut
{
    protected $namespace = 'Chestnut\Auth\Models';

    protected $with = ['roles:id,name'];

    protected $except = ['roles'];

    public function fields(): array
    {
        return [
            Shell::ID(),
            Shell::Text('email', '电子邮箱')->rules("account"),
            Shell::Text("phone", "手机号")->readonly()->rules('account'),
            Shell::Text("name", "昵称")->sortable()
                ->rules('required'),
            Shell::Avatar("avatar", "头像")->rules('required'),
            Shell::Password('password', '密码')->rules('password'),
            Shell::HasManyThrough('roles', '角色'),
            Shell::CreatedAt("注册时间"),
        ];
    }

    public function group()
    {
        return "权限管理";
    }

    public function title()
    {
        return "管理员";
    }
}
