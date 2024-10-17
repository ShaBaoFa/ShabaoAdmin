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
use App\Request\ExhLibObjRequest;
use App\Service\ExhLibObjService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/libManage/libExhibition'),Auth]
class ObjController extends BaseController
{
    #[Inject]
    protected ExhLibObjService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('libExhibition, libExhibition:index')]
    public function index(): ResponseInterface
    {
        return $this->response->success($this->service->index($this->request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('public_index')]
    public function select(): ResponseInterface
    {
        return $this->response->success($this->service->getPublicIndex($this->request->all()));
    }

    #[GetMapping('info/{id:\d+}')]
    public function info(int $id): ResponseInterface
    {
        return $this->response->success($this->service->info($id));
    }

    /**
     * 回收站展项列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('libExhibition:recycle')]
    public function recycle(): ResponseInterface
    {
        return $this->response->success($this->service->getListByRecycle($this->request->all()));
    }

    /**
     * 新增展项.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save'), Permission('libExhibition:save'), OperationLog]
    public function save(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新展项.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('update/{id:\d+}'), Permission('libExhibition:update'), OperationLog]
    public function update(int $id, ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量删除展项到回收站.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('delete'), Permission('libExhibition:delete')]
    public function delete(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->delete((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 单个或批量真实删除展项 （清空回收站）.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping('realDelete'), Permission('libExhibition:realDelete'), OperationLog]
    public function realDelete(ExhLibObjRequest $request): ResponseInterface
    {
        $result = $this->service->realDelete((array) $request->input('ids', []));
        return $result ?
            $this->response->success() :
            $this->response->fail();
    }

    /**
     * 单个或批量恢复在回收站的展项.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('recovery'), Permission('libExhibition:recovery')]
    public function recovery(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->recovery((array) $request->input('ids', [])) ? $this->response->success() : $this->response->fail();
    }

    /**
     * 更改展项状态
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('changeStatus'), Permission('libExhibition:changeStatus'), OperationLog]
    public function changeStatus(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->changeStatus((int) $request->input('id'), (string) $request->input('status'))
            ? $this->response->success() : $this->response->fail();
    }

    #[PutMapping('addStar')]
    public function addStar(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->addStar((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    #[PutMapping('cancelStar')]
    public function cancelStar(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->cancelStar((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    #[PutMapping('addCollection')]
    public function addCollection(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->addCollection((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    #[PutMapping('cancelCollection')]
    public function cancelCollection(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->cancelCollection((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    #[PutMapping('addPick')]
    public function addPick(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->addPick((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    #[PutMapping('cancelPick')]
    public function cancelPick(ExhLibObjRequest $request): ResponseInterface
    {
        return $this->service->cancelPick((int) $request->input('id'))
            ? $this->response->success() : $this->response->fail();
    }

    /**
     * 数字运算操作.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping('numberOperation'), Permission('libExhibition:update'), OperationLog]
    public function numberOperation(): ResponseInterface
    {
        return $this->service->numberOperation(
            (int) $this->request->input('id'),
            (string) $this->request->input('numberName'),
            (int) $this->request->input('numberValue', 1),
        ) ? $this->response->success() : $this->response->fail();
    }
}
