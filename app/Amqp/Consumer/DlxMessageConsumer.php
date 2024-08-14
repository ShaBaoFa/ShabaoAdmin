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
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'dlx_exchange', routingKey: 'dlx_routing_key', queue: 'dlx_queue', name: 'DlxMessageConsumer', nums: 1)]
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
