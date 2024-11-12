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
use App\Service\StatisticsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/statistics')]
class StatisticsController extends BaseController
{
    #[Inject]
    protected StatisticsService $service;

    /**
     * ip ur pv.
     */
    #[GetMapping('visit')]
    public function visit(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->visit());
    }

    #[GetMapping('regionActive')]
    public function regionActive(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->regionActive($request->all()));
    }

    #[GetMapping('user')]
    public function user(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->user());
    }

    #[GetMapping('visitStatistics')]
    public function visitStatistics(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->visitStatistics());
    }

    #[GetMapping('fileUploadStatistics')]
    public function fileUploadStatistics(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->fileUploadStatistics($request->all()));
    }

    #[GetMapping('fileDownloadStatistics')]
    public function fileDownloadStatistics(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->fileDownloadStatistics($request->all()));
    }

    #[GetMapping('rentStatistics')]
    public function rentStatistics(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->rentStatistics($request->all()));
    }

    #[GetMapping('rentRanking')]
    public function rentRanking(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->rentRanking($request->all()));
    }

    #[GetMapping('activeUserRankingByPoint')]
    public function activeUserRankingByPoint(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->activeUserRankingByPoint($request->all()));
    }

    // 收藏展项排行
    #[GetMapping('collectObjectRanking')]
    public function collectObjectRanking(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->collectObjectRanking($request->all()));
    }

    // 收藏展项排行
    #[GetMapping('downloadObjectRanking')]
    public function downloadObjectRanking(RequestInterface $request): ResponseInterface
    {
        return $this->response->success($this->service->downloadObjectRanking($request->all()));
    }
}
