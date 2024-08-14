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

namespace App\Interfaces;

use App\Vo\QueueMessageVo;
use Hyperf\Amqp\Message\ProducerMessageInterface;

interface QueueLogServiceInterface
{
    public function pushMessage(ProducerMessageInterface $producer, QueueMessageVo $messageVo, array $option = []): bool;
}
