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
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Helper\currentUser;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

if (! function_exists('user')) {
    /**
     * 获取当前登录用户实例.
     */
    function user(?string $scene = 'default'): currentUser
    {
        try {
            return new currentUser($scene);
        } catch (ContainerExceptionInterface|NotFoundExceptionInterface $e) {
            throw new BusinessException(ErrorCode::SERVER_ERROR);
        }
    }
}

if (! function_exists('blank')) {
    /**
     * 判断给定的值是否为空.
     */
    function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (! function_exists('filled')) {
    /**
     * 判断给定的值是否不为空.
     */
    function filled(mixed $value): bool
    {
        return ! blank($value);
    }
}
