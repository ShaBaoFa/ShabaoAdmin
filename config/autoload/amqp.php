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
use Hyperf\Amqp\IO\IOFactory;

use function Hyperf\Support\env;

return [
    'enable' => env('AMQP_ENABLE'),
    'default' => [
        'host' => env('AMQP_HOST', 'localhost'),
        'port' => (int) env('AMQP_PORT', 5672),
        'user' => env('AMQP_USER', 'guest'),
        'password' => env('AMQP_PASSWORD', 'guest'),
        'vhost' => env('AMQP_VHOST', '/'),
        'open_ssl' => false,
        'concurrent' => [
            'limit' => 2,
        ],
        'pool' => [
            'connections' => 2,
        ],
        'io' => IOFactory::class,
        'params' => [
            'insist' => false,
            'login_method' => 'AMQPLAIN',
            'login_response' => null,
            'locale' => 'en_US',
            'connection_timeout' => 30.0,
            // 尽量保持是 heartbeat 数值的两倍
            'read_write_timeout' => 60.0,
            'context' => null,
            'keepalive' => true,
            // 尽量保证每个消息的消费时间小于心跳时间
            'heartbeat' => 30,
            'channel_rpc_timeout' => 0.0,
            'close_on_destruct' => false,
            'max_idle_channels' => 10,
        ],
    ],
];
