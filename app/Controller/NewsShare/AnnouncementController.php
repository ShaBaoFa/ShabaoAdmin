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

namespace App\Controller\NewsShare;

use App\Annotation\Auth;
use App\Annotation\OperationLog;
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\AnnouncementRequest;
use App\Request\RentApprovalRequest;
use App\Service\AnnouncementService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/infoManage/announcement'),Auth]
class AnnouncementController extends BaseController
{
    #[Inject]
    protected AnnouncementService $service;

    /**
     * 列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('infoManage:announcement, infoManage:announcement:index')]
    public function index(AnnouncementRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->index($request->all()));
    }

    /**
     * 审核列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('auditIndex'), Permission('infoManage:announcement, infoManage:announcement:auditIndex')]
    public function auditIndex(AnnouncementRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->auditIndex($request->all()));
    }

    #[GetMapping('info/{id:\d+}')]
    public function info(int $id): ResponseInterface
    {
        return $this->response->success($this->service->info($id));
    }

    /**
     * 公开列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('publicIndex')]
    public function publicIndex(AnnouncementRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->publicIndex($request->all()));
    }

    /**
     * 创建.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('infoManage:announcement:save'), OperationLog]
    public function save(AnnouncementRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 编辑.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id:\d+}'), Permission('infoManage:announcement:update')]
    public function update(int $id, AnnouncementRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all())
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 删除.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('organizations:delete')]
    public function delete(AnnouncementRequest $request): ResponseInterface
    {
        return $this->service->delete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 审核.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeAuditStatus'), Permission('infoManage:announcement:changeAuditStatus'), OperationLog]
    public function changeStatus(RentApprovalRequest $request): ResponseInterface
    {
        return $this->service->changeAuditStatus((int) $request->input('id'), (int) $request->input('audit_status'), (string) $request->input('refuse_reason'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 取消.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('cancel'), Permission('infoManage:announcement:cancel')]
    public function cancel(RentApprovalRequest $request): ResponseInterface
    {
        return $this->service->cancel((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }
}
