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

use App\Constants\ConsumerStatusCode;
use App\Interfaces\QueueLogServiceInterface;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Collection\Arr;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'dlx_exchange', routingKey: 'dlx_routing_key', queue: 'dlx_queue', name: 'DlxMessageConsumer', nums: 1)]
class DlxMessageConsumer extends ConsumerMessage
{
    public function __construct(
        private readonly QueueLogServiceInterface $service,
    ) {
    }

    public function consumeMessage($data, AMQPMessage $message): Result
    {
        $consumeStatus = ['consume_status' => ConsumerStatusCode::CONSUME_STATUS_FAIL->value];
        $queueId = Arr::get($data, 'queue_id');
        $this->service->update(
            $queueId,
            $consumeStatus
        );
        return Result::ACK;
    }

    public function isEnable(): bool
    {
        return true;
    }
}
