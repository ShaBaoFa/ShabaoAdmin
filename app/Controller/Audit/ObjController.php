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

namespace App\Controller\Audit;

use App\Annotation\Auth;
use App\Annotation\OperationLog;
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\ExhLibApprovalRequest;
use App\Service\ExhLibObjService;
use App\Service\ObjDlApprovalService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/approvalManage/LibExhibition'),Auth]
class ObjController extends BaseController
{
    #[Inject]
    protected ExhLibObjService $upService;

    #[Inject]
    protected ObjDlApprovalService $dlService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('downloadIndex'), Permission('approvalManage:LibExhibition, approvalManage:LibExhibition:index')]
    public function dlIndex(ExhLibApprovalRequest $request): ResponseInterface
    {
        return $this->response->success($this->dlService->auditIndex($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('uploadIndex'), Permission('approvalManage:LibExhibition, approvalManage:LibExhibition:index')]
    public function upIndex(ExhLibApprovalRequest $request): ResponseInterface
    {
        return $this->response->success($this->upService->auditIndex($request->all()));
    }

    /**
     * 更改展项状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeDownloadAuditStatus'), Permission('approvalManage:LibExhibition:changeAuditStatus'), OperationLog]
    public function changeStatus(ExhLibApprovalRequest $request): ResponseInterface
    {
        return $this->dlService->changeAuditStatus((int) $request->input('id'), (int) $request->input('audit_status'), (string) $request->input('refuse_reason'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 更改展项状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeUploadAuditStatus'), Permission('approvalManage:LibExhibition:changeAuditStatus'), OperationLog]
    public function changeUpAuditStatus(ExhLibApprovalRequest $request): ResponseInterface
    {
        return $this->upService->changeAuditStatus((int) $request->input('id'), (int) $request->input('audit_status'), (string) $request->input('refuse_reason'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 创建下载申请单.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('createDownloadApproval')]
    public function createDownloadApproval(ExhLibApprovalRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->dlService->save($request->all())]);
    }

    /**
     * 取消下载申请单.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('cancelDownloadApproval')]
    public function cancelDownloadApproval(ExhLibApprovalRequest $request): ResponseInterface
    {
        return $this->dlService->cancelDownloadApproval((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 我的下载申请单.
     */
    #[GetMapping('myDownloadApproval')]
    public function myDownloadApproval(ExhLibApprovalRequest $request): ResponseInterface
    {
        return $this->response->success($this->dlService->myDownloadApproval($request->all()));
    }
}
