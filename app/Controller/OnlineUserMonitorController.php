<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Controller;

use App\Annotation\Auth;
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

/**
 * 在线用户监控
 * Class OnlineUserMonitorController.
 */
#[Controller(prefix: 'api/v1/monitor/online_users'), Auth]
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
    #[GetMapping('index')]
    public function getPageList(): ResponseInterface
    {
        return $this->response->success($this->service->getOnlineUserPageList($this->request->all()));
    }

    /**
     * 强退用户.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws \RedisException
     */
    #[PostMapping('kick')]
    public function kickUser(): ResponseInterface
    {
        return $this->service->kickUser((string) $this->request->input('id')) ?
            $this->response->success() : $this->response->fail();
    }
}
