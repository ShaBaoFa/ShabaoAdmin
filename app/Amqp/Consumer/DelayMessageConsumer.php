<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Carbon\Carbon;
use Hyperf\Amqp\Message\ConsumerDelayedMessageTrait;
use Hyperf\Amqp\Message\ProducerDelayedMessageTrait;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;
use function Hyperf\Config\config;

#[Consumer(exchange: 'web-api.delay', routingKey: 'delay.message.routing', queue: 'delay.message.queue', name: "DelayMessageConsumer", nums: 1)]
class DelayMessageConsumer extends ConsumerMessage
{
    use ProducerDelayedMessageTrait;
    use ConsumerDelayedMessageTrait;
    public function consumeMessage($data, AMQPMessage $message): Result
    {
        var_dump($data, 'delay+direct consumeTime:' . Carbon::now()->toDateTimeString());
        return Result::ACK;
    }

    public function isEnable(): bool
    {
        return true;
    }
}
