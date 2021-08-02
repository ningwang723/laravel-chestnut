<?php

namespace Chestnut\Auth\Models;

use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    protected $dateFormat = "Y-m-d H:i:s";
}
