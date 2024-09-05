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

use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\DiskFileShareRequest;
use App\Service\DiskFileShareService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RedisException;

#[Controller(prefix: 'api/v1/diskFileShares')]
class DiskFileShareController extends BaseController
{
    #[Inject]
    protected DiskFileShareService $service;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @description 生成文件或文件夹的分享链接
     * 批量操作：接受多个对象 id.
     */
    #[PostMapping('save'), Permission('disks:share:save')]
    public function share(DiskFileShareRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->save($request->all()));
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @description 删除分享链接
     * 批量操作：接受多个对象 id.
     */
    #[DeleteMapping('delete'), Permission('disks:share:delete')]
    public function delete(DiskFileShareRequest $request): ResponseInterface
    {
        $ids = $request->input('ids');
        return $this->service->delete((array) $ids) ? $this->response->success() : $this->response->fail();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    #[GetMapping('info/{id:\d+}'), Permission('disks:share:info')]
    public function info(int $id, DiskFileShareRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->info($id));
    }

    #[GetMapping('index'), Permission('disks:share,disks:share:index')]
    public function index(DiskFileShareRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getList($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    #[GetMapping('shareLink')]
    public function getShareByLink(DiskFileShareRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getShareByLink($request->all()));
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     */
    #[GetMapping('downloadToken')]
    public function getShareDownloadToken(DiskFileShareRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getShareDownloadToken($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('hash/folder/{folder_id:\d+}')]
    public function getFolderHash(int $folder_id, DiskFileShareRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getHash($folder_id, $request->all()));
    }
}
