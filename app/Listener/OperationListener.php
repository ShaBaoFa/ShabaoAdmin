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

use App\Events\Operation;
use App\Service\OperationLogService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Listener]
class OperationListener implements ListenerInterface
{
    protected array $ignoreRouter = [];

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            Operation::class,
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(object $event): void
    {
        $requestInfo = $event->getRequestInfo();
        if (! in_array($requestInfo['router'], $this->ignoreRouter)) {
            $service = $this->container->get(OperationLogService::class);
            $requestInfo['request_data'] = json_encode($requestInfo['request_data'], JSON_UNESCAPED_UNICODE);
            $service->save($requestInfo);
        }
    }
}
