<?php

declare(strict_types=1);
/**
 * This file is part of web-api.
 *
 * @link     https://blog.wlfpanda1012.com/
 * @github   https://github.com/ShaBaoFa
 * @gitee    https://gitee.com/wlfpanda/web-api
 * @contact  mail@wlfpanda1012.com
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
