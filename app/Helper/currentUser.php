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

namespace App\Helper;

use App\Constants\AuthGuardType;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
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
            throw new BusinessException(ErrorCode::UNAUTHORIZED);
        }
    }
}
