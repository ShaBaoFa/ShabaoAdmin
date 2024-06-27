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

namespace App\Controller;

use App\Annotation\Auth;
use App\Request\AuthRequest;
use App\Service\AuthService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller as CA;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Swow\Psr7\Message\ResponsePlusInterface;

#[CA(prefix: 'api/v1/auth')]
class LoginController extends Controller
{
    #[Inject]
    protected AuthService $authService;

    #[Inject]
    protected UserService $userService;

    //    public function register(AuthRequest $request): ResponsePlusInterface
    //    {
    //        $token = $this->authService->register($request->all());
    //        return $this->response->success($token);
    //    }

    #[PostMapping('login')]
    public function login(AuthRequest $request): ResponsePlusInterface
    {
        return $this->response->success($this->authService->login($request->all()));
    }

    #[DeleteMapping('logout'),Auth]
    public function logout(): ResponsePlusInterface
    {
        $this->authService->logout();
        return $this->response->success();
    }

    #[GetMapping('self'),Auth]
    public function self(): ResponsePlusInterface
    {
        $resource = $this->userService->info($this->authService->checkAndGetId());
        return $this->response->success($resource);
    }
}
