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

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Countable;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;
use Hyperf\WebSocketServer\Sender;
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

if (! function_exists('lang')) {
    /**
     * 获取当前语言
     */
    function lang(): string
    {
        $acceptLanguage = di()
            ->get(RequestInterface::class)
            ->getHeaderLine('accept-language');
        return str_replace('-', '_', ! empty($acceptLanguage) ? explode(',', $acceptLanguage)[0] : 'zh_CN');
    }
}

if (! function_exists('trans')) {
    /**
     * 翻译.
     */
    function trans(string $key, array $replace = []): string
    {
        return \Hyperf\Translation\trans($key, $replace, lang());
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

if (! function_exists('format_size')) {
    /**
     * 格式化大小.
     */
    function format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; ++$i) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}

if (! function_exists('console')) {
    /**
     * 获取控制台输出实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function console(): StdoutLoggerInterface
    {
        return di()->get(StdoutLoggerInterface::class);
    }
}

if (! function_exists('redis')) {
    /**
     * 获取Redis实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function redis(): Redis
    {
        return di()->get(Redis::class);
    }
}

if (! function_exists('ws_sender')) {
    /**
     * 获取websocket发送实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function ws_sender(): Sender
    {
        return di()->get(Sender::class);
    }
}
