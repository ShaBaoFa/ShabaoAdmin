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

namespace App\Helper;

use App\Constants\AuthGuardType;
use App\Constants\ErrorCode;
use App\Exception\AuthException;
use Qbhy\HyperfAuth\AuthGuard;
use Qbhy\HyperfAuth\AuthManager;

class currentUser
{
    protected AuthGuard $guard;

    public function __construct(AuthGuardType $guard = AuthGuardType::JWT)
    {
        $this->guard = di()->get(AuthManager::class)->guard($guard->value);
    }

    public function getGuard(): AuthGuard
    {
        return $this->guard;
    }

    public function getId(): int
    {
        return $this->guard->id();
    }

    public function refresh(): void
    {
        $this->guard->refresh();
    }

    public function check(): void
    {
        if (! $this->guard->check()) {
            throw new AuthException(ErrorCode::UNAUTHORIZED);
        }
    }
}
