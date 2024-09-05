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

namespace App\Listener;

use App\Events\AfterLogin;
use App\Service\UserService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

use function App\Helper\redis;
use function Hyperf\Config\config;

#[Listener]
class PointOperListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            AfterLogin::class,
        ];
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     */
    public function process(object $event): void
    {
        if ($event instanceof AfterLogin) {
            if (! $event->loginStatus) {
                return;
            }
            // 通过redis判断今日该uid是否已经登录过.
            $key = sprintf('%sPoint:Uid_%s:Date_%s', config('cache.default.prefix'), $event->userinfo['id'], date('Ymd'));
            if (! redis()->set($key, 1, ['nx', 'ex' => 86400])) {
                return;
            }
            // 登录成功，增加积分(1)
            $this->container->get(UserService::class)->numberOperation($event->userinfo['id'], 'point');
        }
        redis()->del(sprintf('%sLoginInfo:UserId_%s', config('cache.default.prefix'), $event->userinfo['id']));
    }
}
