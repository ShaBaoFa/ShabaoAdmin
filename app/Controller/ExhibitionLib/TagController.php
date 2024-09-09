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

namespace App\Controller\ExhibitionLib;

use App\Annotation\Auth;
use App\Annotation\OperationLog;
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\ExhLibTagRequest;
use App\Service\ExhLibTagService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/exhLib/tag'),Auth]
class TagController extends BaseController
{
    #[Inject]
    protected ExhLibTagService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('exhLib:tag, exhLib:tag:index')]
    public function index(): ResponseInterface
    {
        return $this->response->success($this->service->getList($this->request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('select')]
    public function select(): ResponseInterface
    {
        return $this->response->success($this->service->getList($this->request->all()));
    }

    #[GetMapping('info/{id:\d+}'), Permission('exhLib:tag, exhLib:tag:info')]
    public function info(int $id): ResponseInterface
    {
        return $this->response->success($this->service->info($id));
    }

    /**
     * 回收站专区列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('exhLib:tag:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->response->success($this->service->getListByRecycle($this->request->all()));
    }

    /**
     * 新增专区.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('exhLib:tag:save'),OperationLog]
    public function save(ExhLibTagRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新专区.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id:\d+}'), Permission('exhLib:tag:update'), OperationLog]
    public function update(int $id, ExhLibTagRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量删除专区到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('exhLib:tag:delete')]
    public function delete(ExhLibTagRequest $request): ResponseInterface
    {
        return $this->service->delete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量真实删除专区 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('exhLib:tag:realDelete'), OperationLog]
    public function realDelete(ExhLibTagRequest $request): ResponseInterface
    {
        $result = $this->service->realDelete((array) $request->input('ids', []));
        return $result ?
            $this->response->success() :
            $this->response->fail();
    }

    /**
     * 单个或批量恢复在回收站的专区.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('exhLib:tag:recovery')]
    public function recovery(ExhLibTagRequest $request): ResponseInterface
    {
        return $this->service->recovery((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 更改专区状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('exhLib:tag:changeStatus'), OperationLog]
    public function changeStatus(ExhLibTagRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 数字运算操作.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('numberOperation'), Permission('exhLib:tag:update'), OperationLog]
    public function numberOperation(): ResponseInterface
    {
        return $this->service->numberOperation(
            (int) $this->request->input('id'),
            (string) $this->request->input('numberName'),
            (int) $this->request->input('numberValue', 1),
        ) ? $this->response->success() : $this->response->fail();
    }
}
