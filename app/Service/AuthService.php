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
use Baoziyoo\HyperfCaptcha\Captcha;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\InvalidArgumentException;

use function App\Helper\user;
use function Hyperf\Config\config;
use function Hyperf\Support\env;
use function Hyperf\Support\make;

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

    public function captcha(): array
    {
        $captcha = make(Captcha::class);
        if (env('APP_ENV') === 'testing') {
            $code = $captcha->generateCode('1111');
        } else {
            $code = $captcha->generateCode();
        }
        $captchaKey = $this->genCaptchaKey($code);
        $captchaCode = Arr::get($code, 'code');
        $redis = di()->get(Redis::class);
        $key = sprintf('%scaptcha:%s', config('cache.default.prefix'), $captchaKey);
        var_dump($key);
        $redis->setex($key, 300, $captchaCode); // 验证码5分钟有效
        return [
            'captcha_key' => $captchaKey,
            'captcha' => Arr::get($code,'base64'),
        ];
    }

    private function genCaptchaKey(array $code): string
    {
        return 'captcha_' . md5(Arr::get($code, 'code') . '_' . Carbon::now()->timestamp);
    }

    private function formatToken(string $token): array
    {
        return [
            'token_type' => 'Bearer',
            'access_token' => $token,
        ];
    }
}
