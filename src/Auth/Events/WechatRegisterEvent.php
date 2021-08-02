<?php

namespace Chestnut\Auth\Events;

use Illuminate\Queue\SerializesModels;

class WechatRegisterEvent
{
    use SerializesModels;

    public $user;
    public $parent_code;

    public function __construct($user, $parent_code)
    {
        $this->user = $user;
        $this->parent_code = $parent_code;
    }
}
