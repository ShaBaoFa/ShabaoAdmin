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

namespace App\Controller;

use App\Annotation\Auth;
use App\Base\BaseController;
use App\Request\UserRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Swow\Psr7\Message\ResponsePlusInterface;

#[Controller(prefix: 'api/v1/users')]
class UserController extends BaseController
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
