<?php

namespace Chestnut\Auth\Nuts;

use Chestnut\Dashboard\Nut;
use Chestnut\Facades\Shell;
use Spatie\Permission\Models\Permission;

class Role extends Nut
{
    //
    protected $namespace = 'Chestnut\Auth\Models';

    protected $except = ['permissions'];

    protected $with = ['permissions:id,name'];

    public function boot()
    {
        $model = $this->getModelName();

        $model::saved(function ($model) {
            $permissions       = $this->getExceptProp('permissions');
            $gavePermissions   = $model->getPermissionNames();
            $removePermissions = $gavePermissions->diff($permissions);

            foreach ($removePermissions as $remove) {
                $model->revokePermissionTo($remove);
            }

            if (empty($permissions)) {
                return;
            }

            $permissions = collect($permissions)->map(function ($permission) {
                return Permission::firstOrCreate(['name' => $permission]);
            });

            $model->givePermissionTo($permissions);
        });
    }

    public function getExceptProp($prop)
    {
        if (!in_array($prop, $this->except)) {
            throw new \Error("prop [$prop] not except");
        }

        $except = app('request')->only($this->except);

        return isset($except[$prop]) ? $except[$prop] : [];
    }

    public function fields(): array
    {
        return [
            Shell::ID(),
            Shell::Text("name", "名称"),
            Shell::HasManyThrough("permissions", "权限")->hideFromIndex(),
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
