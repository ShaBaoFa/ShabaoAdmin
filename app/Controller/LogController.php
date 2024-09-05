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
use App\Service\LoginLogService;
use App\Service\OperationLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/logs'), Auth]
class LogController extends BaseController
{
    #[Inject]
    protected LoginLogService $loginLogService;

    #[Inject]
    protected OperationLogService $operationLogService;

    /**
     * 获取登录日志列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getLoginLogPageList'), Permission('system:loginLog')]
    public function getLoginLogPageList(): ResponseInterface
    {
        return $this->response->success($this->loginLogService->getPageList($this->request->all()));
    }

    /**
     * 获取操作日志列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getOperLogPageList'), Permission('system:operLog')]
    public function getOperLogPageList(): ResponseInterface
    {
        return $this->response->success($this->operationLogService->getPageList($this->request->all()));
    }
}
