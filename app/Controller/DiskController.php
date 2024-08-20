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
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\DiskRequest;
use App\Service\DiskService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/disks'),Auth]
class DiskController extends BaseController
{
    #[Inject]
    protected DiskService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index'), Permission('disks, disks:index')]
    public function index(DiskRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getList($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('recycle'), Permission('disks:recycle')]
    public function recycleTree(DiskRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getListByRecycle($request->all()));
    }
}
