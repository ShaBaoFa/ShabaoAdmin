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

namespace App\Base;

use Hyperf\Collection\Arr;
use Hyperf\HttpServer\Request;

class BaseRequest extends Request
{
    /**
     * 获取请求IP.
     */
    public function ip(): string
    {
        $ip = $this->getServerParams()['remote_addr'] ?? '0.0.0.0';
        $headers = $this->getHeaders();

        if (Arr::has($headers, 'x-real-ip')) {
            $ip = $headers['x-real-ip'][0];
        } elseif (Arr::has($headers, 'x-forwarded-for')) {
            $ip = $headers['x-forwarded-for'][0];
        } elseif (Arr::has($headers, 'http_x_forwarded_for')) {
            $ip = $headers['http_x_forwarded_for'][0];
        }

        return $ip;
    }

    /**
     * 获取协议架构.
     */
    public function getScheme(): string
    {
        if (isset($this->getHeader('X-scheme')[0])) {
            return $this->getHeader('X-scheme')[0] . '://';
        }
        return 'http://';
    }
}
