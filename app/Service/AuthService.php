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

namespace App\Service;

use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthManager;

class AuthService extends BaseService
{
    #[Inject]
    protected AuthManager $auth;

    public function jwt(Authenticatable $model)
    {
        return $this->auth->guard('jwt')->login($model);
    }

    public function user(): ?Authenticatable
    {
        return $this->auth->guard('jwt')->user();
    }

    public function logout()
    {
        return $this->auth->guard('jwt')->logout();
    }
}
