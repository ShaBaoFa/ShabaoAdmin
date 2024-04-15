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

use App\Constants\AuthGuardType;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Service\Dao\UserDao;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\AuthManager;

class AuthService extends BaseService
{
    #[Inject]
    protected AuthManager $auth;

    #[Inject]
    protected UserDao $userDao;

    public function register(array $input, AuthGuardType $guard = AuthGuardType::JWT): array
    {
        $model = $this->userDao->save($input);
        return $this->formatToken($this->auth->guard($guard->value)->login($model), $guard);
    }

    public function login(array $input, AuthGuardType $guard = AuthGuardType::JWT): array
    {
        $model = $this->userDao->findByAccount($input, true);
        return $this->formatToken($this->auth->guard($guard->value)->login($model), $guard);
    }

    public function logout(AuthGuardType $guard = AuthGuardType::JWT)
    {
        return $this->auth->guard($guard->value)->logout();
    }

    public function checkAndGetId(AuthGuardType $guard = AuthGuardType::JWT): int
    {
        if (! $this->auth->guard($guard->value)->check()) {
            throw new BusinessException(ErrorCode::UNAUTHORIZED);
        }
        return $this->auth->guard($guard->value)->id();
    }

    private function formatToken(string $token, AuthGuardType $guard = AuthGuardType::JWT): array
    {
        $dataArray = $this->auth->guard($guard->value)->getPayload($token);
        return [
            'token_type' => 'Bearer',
            'expires_in' => Carbon::parse($dataArray['exp'])->toDateTimeString(),
            'access_token' => $token,
        ];
    }
}
