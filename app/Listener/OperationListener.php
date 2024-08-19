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

use App\Amqp\Consumer\OperationLogConsumer;
use App\Amqp\Producer\OperationLogProducer;
use App\Events\Operation;
use App\Service\OperationLogService;
use App\Service\QueueLogService;
use App\Vo\AmqpQueueVo;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

use function Hyperf\Config\config;

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
        /**
         * @var Operation $event
         */
        $requestInfo = $event->getRequestInfo();
        if (! in_array($requestInfo['router'], $this->ignoreRouter)) {
            $service = $this->container->get(OperationLogService::class);
            $requestInfo['request_data'] = json_encode($requestInfo['request_data'], JSON_UNESCAPED_UNICODE);
            if (config('amqp.enable') && $this->container->get(OperationLogConsumer::class)->isEnable()) {
                $amqpQueueVo = new AmqpQueueVo();
                $amqpQueueVo->setProducer(OperationLogProducer::class);
                $amqpQueueVo->setData($requestInfo);
                if (! $this->container->get(QueueLogService::class)->addQueue($amqpQueueVo)) {
                    $this->container->get(LoggerInterface::class)->warning(printf('%s queue log add failed', __METHOD__));
                    $service->save($requestInfo);
                }
            } else {
                $service->save($requestInfo);
            }
        }
    }
}
