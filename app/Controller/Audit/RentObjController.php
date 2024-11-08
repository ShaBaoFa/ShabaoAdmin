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
use App\Request\RentApprovalRequest;
use App\Service\RentApprovalService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/approvalManage/libExhibition/rent'),Auth]
class RentObjController extends BaseController
{
    #[Inject]
    protected RentApprovalService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('approvalManage:LibExhibition:Rent, approvalManage:LibExhibition:Rent:index')]
    public function index(RentApprovalRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->index($request->all()));
    }

    /**
     * 更改展项状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeAuditStatus'), Permission('approvalManage:LibExhibition:Rent:changeAuditStatus'), OperationLog]
    public function changeAuditStatus(RentApprovalRequest $request): ResponseInterface
    {
        return $this->service->changeAuditStatus((int) $request->input('id'), (int) $request->input('audit_status'), (string) $request->input('refuse_reason'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 更改展项状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeRentStatus'), Permission('approvalManage:LibExhibition:Rent:changeRentStatus'), OperationLog]
    public function changeRentStatus(RentApprovalRequest $request): ResponseInterface
    {
        return $this->service->changeRentStatus((int) $request->input('id'), (int) $request->input('rent_status'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 创建下载申请单.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('createApproval')]
    public function createApproval(RentApprovalRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 取消下载申请单.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('cancelApproval')]
    public function cancelApproval(RentApprovalRequest $request): ResponseInterface
    {
        return $this->service->cancelApproval((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 我的下载申请单.
     */
    #[GetMapping('myApproval')]
    public function myRentApproval(RentApprovalRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->myApproval($request->all()));
    }
}
