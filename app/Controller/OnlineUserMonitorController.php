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
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RedisException;

/**
 * 在线用户监控
 * Class OnlineUserMonitorController.
 */
#[Controller(prefix: 'api/v1/monitor/onlineUsers')]
class OnlineUserMonitorController extends BaseController
{
    #[Inject]
    protected UserService $service;

    /**
     * 获取在线用户列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[GetMapping('index'),Permission('monitor:onlineUser, monitor:onlineUser:index')]
    public function getPageList(): ResponseInterface
    {
        return $this->response->success($this->service->getOnlineUserPageList($this->request->all()));
    }

    /**
     * 强退用户.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws RedisException
     */
    #[PostMapping('kick'),Permission('monitor:onlineUser:kick')]
    public function kickUser(): ResponseInterface
    {
        return $this->service->kickUser((string) $this->request->input('id')) ?
            $this->response->success() : $this->response->fail();
    }
}
