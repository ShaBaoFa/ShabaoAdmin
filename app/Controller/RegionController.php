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

use App\Base\BaseController;
use App\Request\RegionRequest;
use App\Service\RegionService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/region')]
class RegionController extends BaseController
{
    #[Inject]
    protected RegionService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('index')]
    public function index(RegionRequest $request): ResponseInterface
    {
        $hashKey = md5(json_encode($request->validated()));
        return $this->response->success($this->service->getRegion($request->validated(), $hashKey));
    }
}
