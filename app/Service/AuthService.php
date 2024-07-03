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
use App\Constants\ErrorCode;
use App\Dao\UserDao;
use App\Events\AfterLogin;
use App\Exception\BusinessException;
use App\Model\User;
use App\Vo\UserServiceVo;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\InvalidArgumentException;

use function user;

class AuthService extends BaseService
{
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function login(UserServiceVo $vo): array
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
        $afterLogin->loginStatus = true;
        $afterLogin->message = '登录成功';
        $token = user()->getToken($afterLogin->userinfo);
        $afterLogin->token = $token;
        $eventDispatcher->dispatch($afterLogin);
        return $this->formatToken($token);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function logout(): void
    {
        user()->getJwt()->logout();
    }

    private function formatToken(string $token): array
    {
        return [
            'token_type' => 'Bearer',
            'access_token' => $token,
        ];
    }
}
