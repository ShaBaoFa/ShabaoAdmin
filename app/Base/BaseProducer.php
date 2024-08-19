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

namespace App\Base;

use Hyperf\Amqp\Message\ProducerMessage;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function App\Helper\console;
use function Hyperf\Config\config;

class BaseProducer extends ProducerMessage
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(mixed $data)
    {
        console()->info(
            sprintf(
                '%s created queue message time at: %s, data is: %s',
                config('base-common.queue_exchange'),
                date('Y-m-d H:i:s'),
                (is_array($data) || is_object($data)) ? json_encode($data) : $data
            )
        );
        $this->payload = $data;
    }

    /**
     * 生成RoutingKey.
     */
    protected function generateRoutingKey(): string
    {
        $fullClassName = get_class($this);
        $parts = explode('\\', $fullClassName);
        $className = end($parts);
        return Str::of($className)->kebab()->replace('-', '.')->replace('producer', 'routing')->toString();
    }
}
