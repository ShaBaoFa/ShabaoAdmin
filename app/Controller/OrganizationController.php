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

use App\Annotation\OperationLog;
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Constants\ErrorCode;
use App\Request\OrganizationRequest;
use App\Service\organizationservice;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/organizations')]
class OrganizationController extends BaseController
{
    #[Inject]
    protected organizationservice $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('organizations, organizations:index')]
    public function index(): ResponseInterface
    {
        return $this->response->success($this->service->getList($this->request->all()));
    }

    /**
     * 回收站组织树列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('organizations:recycle')]
    public function recycleTree(): ResponseInterface
    {
        return $this->response->success($this->service->getListByRecycle($this->request->all()));
    }

    /**
     * 新增组织.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('organizations:save'),OperationLog]
    public function save(OrganizationRequest $request)
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新组织.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id}'), Permission('organizations:update'), OperationLog]
    public function update(int $id, OrganizationRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量删除组织到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('organizations:delete')]
    public function delete(OrganizationRequest $request): ResponseInterface
    {
        return $this->service->delete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量真实删除组织 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('organizations:realDelete'), OperationLog]
    public function realDelete(OrganizationRequest $request): ResponseInterface
    {
        $data = $this->service->realDel((array) $request->input('ids', []));
        return is_null($data) ?
            $this->response->success() :
            $this->response->fail(code: ErrorCode::ORG_CAN_NOT_DELETE, message: ErrorCode::ORG_CAN_NOT_DELETE->getMessage(), data: $data);
    }

    /**
     * 单个或批量恢复在回收站的组织.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('organizations:recovery')]
    public function recovery(OrganizationRequest $request): ResponseInterface
    {
        return $this->service->recovery((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 更改组织状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('organizations:changeStatus'), OperationLog]
    public function changeStatus(OrganizationRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 数字运算操作.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('numberOperation'), Permission('organizations:update'), OperationLog]
    public function numberOperation(): ResponseInterface
    {
        return $this->service->numberOperation(
            (int) $this->request->input('id'),
            (string) $this->request->input('numberName'),
            (int) $this->request->input('numberValue', 1),
        ) ? $this->response->success() : $this->response->fail();
    }
}
