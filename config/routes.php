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
// 消息ws服务器
use App\Middleware\WsAuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::addServer('message', function () {
    Router::get('/ws.message.io', \App\Controller\WsServerController::class, [
        'middleware' => [WsAuthMiddleware::class],
    ]);
});
