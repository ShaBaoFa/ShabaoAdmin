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

use App\Request\UserRequest;
use App\Service\AuthService;
use Hyperf\Di\Annotation\Inject;
use Swow\Psr7\Message\ResponsePlusInterface;

class UserController extends Controller
{
    #[Inject]
    protected AuthService $authService;

    public function self(UserRequest $request): ResponsePlusInterface
    {
        return $this->response->success($this->authService->user());
    }
}
