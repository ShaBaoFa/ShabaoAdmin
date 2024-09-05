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
use App\Controller\WsServerController;
use App\Middleware\WsAuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::get('/', function () {
    return 'see u.';
});

Router::addServer('message', function () {
    Router::get('/ws.message.io', WsServerController::class, [
        'middleware' => [WsAuthMiddleware::class],
    ]);
});
