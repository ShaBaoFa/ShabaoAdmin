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

use App\Request\AuthRequest;
use App\Service\AuthService;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Swow\Psr7\Message\ResponsePlusInterface;

class LoginController extends Controller
{
    #[Inject]
    protected UserService $userService;

    #[Inject]
    protected AuthService $authService;

    public function register(AuthRequest $request): ResponsePlusInterface
    {
        $token = $this->authService->register($request->all());
        return $this->response->success($token);
    }

    public function login(AuthRequest $request): ResponsePlusInterface
    {
        $token = $this->authService->login($request->all());
        return $this->response->success($token);
    }

    public function logout(): ResponsePlusInterface
    {
        $this->authService->logout();
        return $this->response->success();
    }
}
