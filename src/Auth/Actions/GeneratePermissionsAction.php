<?php

namespace Chestnut\Auth\Actions;

use Chestnut\Auth\Models\Permission;
use Chestnut\Dashboard\Action;
use Chestnut\Dashboard\Nut;

class GeneratePermissionsAction extends Action
{
    public function handle($request, Nut $nut)
    {
        $nuts = app('shell')->toArray()['nuts'];

        foreach ($nuts as $nut) {
            foreach ($nut->getViews() as $view) {
                Permission::updateOrCreate(["name" => ucfirst($view) . " {$nut->getRepositoryName()}"]);
            }
        }

        return ['errno' => 0, "message" => "初始化权限完成"];
    }
}
