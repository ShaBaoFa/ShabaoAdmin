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
use App\Request\UserRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller as CA;
use Hyperf\HttpServer\Annotation\PostMapping;
use Swow\Psr7\Message\ResponsePlusInterface;

#[CA(prefix: 'api/v1/users')]
class UserController extends Controller
{
    #[Inject]
    protected UserService $userService;

    #[PostMapping('save'),Auth]
    public function save(UserRequest $request): ResponsePlusInterface
    {
        $resource = $this->userService->save($request->validated());
        return $this->response->success($resource);
    }
}
