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

namespace App\Amqp\Consumer;

use App\Base\BaseConsumer;
use App\Service\OperationLogService;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Di\Annotation\Inject;
use PhpAmqpLib\Wire\AMQPTable;

#[Consumer(exchange: 'web-api', routingKey: 'operation.log.routing', queue: 'operation.log.queue', name: 'OperationLogConsumer', nums: 1)]
class OperationLogConsumer extends BaseConsumer
{
    #[Inject]
    protected OperationLogService $consumeService;

    /**
     * Overwrite.
     */
    public function getQueueBuilder(): QueueBuilder
    {
        return parent::getQueueBuilder()
            ->setArguments(new AMQPTable([
                'x-dead-letter-exchange' => 'dlx_exchange',
                'x-dead-letter-routing-key' => 'dlx_routing_key',
            ]));
    }

    public function isEnable(): bool
    {
        return true;
    }

    protected function process($data): bool
    {
        return $this->consumeService->dao->insertByQueue($data);
    }
}
