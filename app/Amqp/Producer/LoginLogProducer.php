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

use App\Base\BaseProducer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function Hyperf\Config\config;

class LoginLogProducer extends BaseProducer
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(mixed $data)
    {
        $this->setExchange(config('base-common.queue_exchange'));
        $this->setRoutingKey($this->generateRoutingKey());
        parent::__construct($data);
    }
}
