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

use Carbon\Carbon;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

use function Hyperf\Config\config;

#[Consumer(exchange: 'web-api', routingKey: 'message.routing', queue: 'message.queue', name: 'MessageConsumer', nums: 1)]
class MessageConsumer extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): Result
    {
        if (empty($data)){
            return Result::DROP;
        }
        var_dump('success consumeTime:' . Carbon::now()->toDateTimeString());
        return Result::ACK;
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
