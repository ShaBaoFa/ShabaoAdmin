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
use App\Request\NewsRequest;
use App\Request\RentApprovalRequest;
use App\Service\NewsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/infoManage/news'),Auth]
class NewsController extends BaseController
{
    #[Inject]
    protected NewsService $service;

    /**
     * 列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('infoManage:news, infoManage:news:index')]
    public function index(NewsRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->index($request->all()));
    }

    /**
     * 审核列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('auditIndex'), Permission('infoManage:news, infoManage:news:auditIndex')]
    public function auditIndex(NewsRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->auditIndex($request->all()));
    }

    /**
     * 公开列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('publicIndex')]
    public function publicIndex(NewsRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->publicIndex($request->all()));
    }

    /**
     * 创建.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('infoManage:news:save'), OperationLog]
    public function save(NewsRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 编辑.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id:\d+}'), Permission('infoManage:news:update')]
    public function update(int $id, NewsRequest $request): ResponseInterface
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
    public function delete(NewsRequest $request): ResponseInterface
    {
        return $this->service->delete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 审核.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeAuditStatus'), Permission('infoManage:news:changeAuditStatus'), OperationLog]
    public function changeStatus(RentApprovalRequest $request): ResponseInterface
    {
        return $this->service->changeAuditStatus((int) $request->input('id'), (int) $request->input('audit_status'), (string) $request->input('refuse_reason'))
            ? $this->response->success() : $this->response->fail();
    }

    #[GetMapping('info/{id:\d+}')]
    public function info(int $id): ResponseInterface
    {
        return $this->response->success($this->service->info($id));
    }

    /**
     * 取消.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('cancel'), Permission('infoManage:news:cancel')]
    public function cancel(RentApprovalRequest $request): ResponseInterface
    {
        return $this->service->cancel((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }
}
