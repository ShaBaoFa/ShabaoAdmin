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
use App\Annotation\OperationLog;
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\UserRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function App\Helper\user;

#[Controller(prefix: 'api/v1/accountManage/user')]
class UserController extends BaseController
{
    #[Inject]
    protected UserService $service;

    /**
     * 用户列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('accountManage:user, accountManage:user:index')]
    public function index(): ResponseInterface
    {
        return $this->response->success($this->service->getPageList($this->request->all(), false));
    }

    /**
     * 回收站列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('accountManage:user:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->response->success($this->service->getPageListByRecycle($this->request->all()));
    }

    /**
     * 获取一个用户信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('{id:\d+}'), Permission('accountManage:user:info')]
    public function info(int $id): ResponseInterface
    {
        return $this->response->success($this->service->info($id));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'),Permission('accountManage:user:save'), OperationLog]
    public function save(UserRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->validated())]);
    }

    /**
     * 更新一个用户信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('accountManage:user:update'), OperationLog]
    public function update(int $id, UserRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量删除用户到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('accountManage:user:delete')]
    public function delete(UserRequest $request): ResponseInterface
    {
        return $this->service->delete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量真实删除用户 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('accountManage:user:realDelete'), OperationLog]
    public function realDelete(UserRequest $request): ResponseInterface
    {
        return $this->service->realDelete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量恢复在回收站的用户.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('accountManage:user:recovery'), OperationLog]
    public function recovery(UserRequest $request): ResponseInterface
    {
        return $this->service->recovery((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 更改用户状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('accountManage:user:changeStatus'), OperationLog]
    public function changeStatus(UserRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 初始化用户密码
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('initUserPassword'), Permission('accountManage:user:initUserPassword'), OperationLog]
    public function initUserPassword(UserRequest $request): ResponseInterface
    {
        return $this->service->initUserPassword((int) $request->input('id')) ? $this->response->success() : $this->response->fail();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('self'),Auth]
    public function self(): ResponseInterface
    {
        return $this->response->success($this->service->info());
    }

    /**
     * 更改用户资料，含修改头像 (修改自己).
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('self'),Auth]
    public function updateInfo(UserRequest $request): ResponseInterface
    {
        return $this->service->updateInfo(user()->getId(), $request->all()) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 修改密码 (修改自己).
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('self/modifyPassword'),Auth]
    public function modifyPassword(UserRequest $request): ResponseInterface
    {
        return $this->service->modifyPassword($request->validated()) ? $this->response->success() : $this->response->fail();
    }

    #[GetMapping('self/collections'),Auth]
    public function myCollectObjs(UserRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->myCollectObjs($request->all()));
    }

    #[GetMapping('self/myPick'),Auth]
    public function myPickObjs(UserRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->myPickObjs($request->all()));
    }
}
