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
use App\Constants\ConsumerStatusCode;
use App\Events\PrivateMessageSent;
use App\Interfaces\QueueLogServiceInterface;
use App\Service\MessageService;
use Exception;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Amqp\Result;
use Hyperf\Collection\Arr;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

#[Consumer(exchange: 'web-api', routingKey: 'message.routing', queue: 'message.queue', name: 'MessageConsumer', nums: 1)]
class MessageConsumer extends BaseConsumer
{
    public function __construct(
        private readonly QueueLogServiceInterface $service,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MessageService $consumeService
    ) {
        parent::__construct($service, $consumeService);
    }

    /**
     * @param mixed $data
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function consumeMessage($data, AMQPMessage $message): Result
    {
        $result = Result::DROP;
        if (empty($data) && ! Arr::accessible($data)) {
            return $result;
        }
        $queueId = Arr::get($data, 'queue_id');
        try {
            $consumeStatus = ['consume_status' => ConsumerStatusCode::CONSUME_STATUS_FAIL->value];
            if ($this->consumeService->dao->insertByQueue($this->handleData($data))) {
                Arr::set($consumeStatus, 'consume_status', ConsumerStatusCode::CONSUME_STATUS_SUCCESS->value);
                $this->eventDispatcher->dispatch(new PrivateMessageSent(Arr::get($data, 'data')));
                $result = Result::ACK;
            }
            $this->service->update(
                $queueId,
                $consumeStatus
            );
        } catch (Exception $e) {
            $this->service->update(
                $queueId,
                Arr::merge($consumeStatus, ['log_content' => $e->getMessage()])
            );
        }
        return $result;
    }

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
}
