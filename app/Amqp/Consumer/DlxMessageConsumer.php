<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Carbon\Carbon;
use Hyperf\Amqp\Message\Type;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;
use function Hyperf\Config\config;

#[Consumer(exchange: 'dlx_exchange', routingKey: 'dlx_routing_key', queue: 'dlx_queue', name: "DlxMessageConsumer", nums: 1)]
class DlxMessageConsumer extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): Result
    {
        var_dump('死信 consumeTime:' . Carbon::now()->toDateTimeString());
        return Result::ACK;
    }

    public function isEnable(): bool
    {
        return true;
    }
}