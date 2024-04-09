<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use App\Controller\LoginController;
use App\Controller\UserController;
use Hyperf\HttpServer\Router\Router;
use Qbhy\HyperfAuth\AuthMiddleware;

Router::addGroup('/api/v1', function () {
    Router::post('/register', [LoginController::class, 'register']);
    Router::post('/login', [LoginController::class, 'login']);
    Router::delete('/logout', [LoginController::class, 'logout']);

    // 个人中心
    Router::addGroup('/self', function () {
        Router::get('', [UserController::class, 'self'], ['middleware' => [AuthMiddleware::class]]);
    });

    // Add more routes here
});
