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

namespace App\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerDelayedMessageTrait;
use Hyperf\Amqp\Message\ProducerMessage;
use Hyperf\Amqp\Message\Type;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function App\Helper\console;

#[Producer(exchange: 'web-api.delay', routingKey: 'delay.message.routing')]
class DelayedMessageProducer extends ProducerMessage
{
    use ProducerDelayedMessageTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(mixed $data)
    {
        console()->info(
            sprintf(
                'web-api created delay queue message time at: %s, data is: %s',
                date('Y-m-d H:i:s'),
                (is_array($data) || is_object($data)) ? json_encode($data) : $data
            )
        );
        $this->payload = $data;
    }
}
