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

use App\Base\BaseRequest;
use App\Constants\ErrorCode;
use App\Exception\AuthException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;
use Xmo\JWTAuth\JWT;

use function Hyperf\Support\env;

class currentUser
{
    protected JWT $jwt;

    protected BaseRequest $request;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(string $scene = 'default')
    {
        $this->jwt = di()->get(JWT::class)->setScene($scene);
    }

    /**
     * 验证token.
     */
    public function check(?string $token = null, string $scene = 'default'): bool
    {
        try {
            if ($this->jwt->checkToken($token, $scene, true, true, true)) {
                return true;
            }
        } catch (InvalidArgumentException|Throwable $e) {
            throw new AuthException(ErrorCode::UNAUTHORIZED);
        }

        return false;
    }

    /**
     * 获取JWT对象
     */
    public function getJwt(): JWT
    {
        return $this->jwt;
    }

    /**
     * 获取当前登录用户信息.
     */
    public function getUserInfo(?string $token = null): array
    {
        return $this->jwt->getParserData($token);
    }

    /**
     * 获取当前登录用户ID.
     */
    public function getId(): int
    {
        return $this->jwt->getParserData()['id'];
    }

    /**
     * 获取当前登录用户名.
     */
    public function getUsername(): string
    {
        return $this->jwt->getParserData()['username'];
    }

    /**
     * 获取当前token用户类型.
     */
    public function getUserType(): string
    {
        return $this->jwt->getParserData()['user_type'];
    }

    /**
     * 是否为超级管理员（创始人），用户禁用对创始人没用.
     */
    public function isSuperAdmin(): bool
    {
        return env('SUPER_ADMIN') == $this->getId();
    }

    /**
     * 获取Token.
     * @throws InvalidArgumentException
     */
    public function getToken(array $user): string
    {
        return $this->jwt->getToken($user);
    }

    /**
     * 刷新token.
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        return $this->jwt->refreshToken();
    }
}
