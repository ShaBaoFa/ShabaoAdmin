<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use App\Constants\AuthGuardType;
use App\Helper\currentUser;

if (! function_exists('user')) {
    /**
     * 获取当前登录用户实例.
     */
    function user(AuthGuardType $guardType = AuthGuardType::JWT): currentUser
    {
        return new currentUser($guardType);
    }
}
