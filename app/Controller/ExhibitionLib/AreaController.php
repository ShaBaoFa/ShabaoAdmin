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
use App\Request\ExhLibAreaRequest;
use App\Service\ExhLibAreaService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/libManage/precinct'),Auth]
class AreaController extends BaseController
{
    #[Inject]
    protected ExhLibAreaService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('libManage:precinct, libManage:precinct:index')]
    public function index(): ResponseInterface
    {
        return $this->response->success($this->service->getPageList($this->request->all()));
    }

    #[GetMapping('select')]
    public function select(): ResponseInterface
    {
        return $this->response->success($this->service->getList($this->request->all()));
    }

    #[GetMapping('info/{id:\d+}'), Permission('libManage:precinct, libManage:precinct:info')]
    public function info(int $id): ResponseInterface
    {
        return $this->response->success($this->service->info($id));
    }

    /**
     * 回收站专区列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('libManage:precinct:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->response->success($this->service->getListByRecycle($this->request->all()));
    }

    /**
     * 新增专区.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('libManage:precinct:save'),OperationLog]
    public function save(ExhLibAreaRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新专区.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id:\d+}'), Permission('libManage:precinct:update'), OperationLog]
    public function update(int $id, ExhLibAreaRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量删除专区到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('libManage:precinct:delete')]
    public function delete(ExhLibAreaRequest $request): ResponseInterface
    {
        return $this->service->delete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量真实删除专区 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('libManage:precinct:realDelete'), OperationLog]
    public function realDelete(ExhLibAreaRequest $request): ResponseInterface
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
    #[PutMapping('recovery'), Permission('libManage:precinct:recovery')]
    public function recovery(ExhLibAreaRequest $request): ResponseInterface
    {
        return $this->service->recovery((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 更改专区状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('libManage:precinct:changeStatus'), OperationLog]
    public function changeStatus(ExhLibAreaRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 数字运算操作.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('numberOperation'), Permission('libManage:precinct:update'), OperationLog]
    public function numberOperation(): ResponseInterface
    {
        return $this->service->numberOperation(
            (int) $this->request->input('id'),
            (string) $this->request->input('numberName'),
            (int) $this->request->input('numberValue', 1),
        ) ? $this->response->success() : $this->response->fail();
    }
}
