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
use App\Events\PrivateMessageSent;
use App\Service\MessageService;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\Inject;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\EventDispatcher\EventDispatcherInterface;

#[Consumer(exchange: 'web-api', routingKey: 'message.routing', queue: 'message.queue', name: 'MessageConsumer', nums: 1)]
class MessageConsumer extends BaseConsumer
{
    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;

    #[Inject]
    protected MessageService $consumeService;

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
        if ($this->consumeService->dao->insertByQueue($data)) {
            $this->eventDispatcher->dispatch(new PrivateMessageSent(Arr::get($data, 'data')));
            return true;
        }
        return false;
    }
}
