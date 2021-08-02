<?php

namespace Chestnut\Auth\Models;

use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $dateFormat = "Y-m-d H:i:s";
}
