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

namespace App\Service;

use App\Base\BaseService;
use App\Constants\AuthGuardType;
use App\Constants\ErrorCode;
use App\Dao\UserDao;
use App\Events\AfterLogin;
use App\Exception\AuthException;
use App\Exception\BusinessException;
use App\Model\User;
use App\Vo\UserServiceVo;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qbhy\HyperfAuth\AuthManager;

class AuthService extends BaseService
{
    #[Inject]
    protected AuthManager $auth;

    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    public function register(array $input, AuthGuardType $guard = AuthGuardType::JWT): array
    {
        $model = $this->dao->save($input);
        return $this->formatToken($this->auth->guard($guard->value)->login($model), $guard);
    }

    public function login(UserServiceVo $vo, AuthGuardType $guard = AuthGuardType::JWT): array
    {
        $model = $this->dao->findByUsername($vo->getUsername(), true);
        $eventDispatcher = di()->get(EventDispatcherInterface::class);
        $afterLogin = new AfterLogin($model->toArray());
        if (password_verify(password: $vo->getPassword(), hash: $model->password) === false) {
            $afterLogin->message = '用户名或密码错误';
            $eventDispatcher->dispatch($afterLogin);
            throw new BusinessException(ErrorCode::USER_PASSWORD_ERROR);
        }
        if ($model->status === User::STATUS_DISABLE) {
            $afterLogin->message = '用户已被禁用';
            $eventDispatcher->dispatch($afterLogin);
            throw new BusinessException(ErrorCode::USER_BAN);
        }
        $token = $this->auth->guard($guard->value)->login($model);
        $afterLogin->loginStatus = true;
        $afterLogin->token = $token;
        $afterLogin->message = '登录成功';
        $eventDispatcher->dispatch($afterLogin);
        return $this->formatToken($this->auth->guard($guard->value)->login($model), $guard);
    }

    public function logout(AuthGuardType $guard = AuthGuardType::JWT)
    {
        return $this->auth->guard($guard->value)->logout();
    }

    public function checkAndGetId(AuthGuardType $guard = AuthGuardType::JWT): int
    {
        if (! $this->auth->guard($guard->value)->check()) {
            throw new AuthException(ErrorCode::UNAUTHORIZED);
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
