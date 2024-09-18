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
use App\Service\DiskFileShareService;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\Inject;
use PhpAmqpLib\Wire\AMQPTable;

#[Consumer(exchange: 'web-api', routingKey: 'disk.file.share.op.count.routing', queue: 'disk.file.share.op.count.queue', name: 'DiskFileShareOpCountConsumer', nums: 1)]
class DiskFileShareOpCountConsumer extends BaseConsumer
{
    #[Inject]
    protected DiskFileShareService $consumeService;

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

    protected function process(array $data): bool
    {
        return $this->consumeService->dao->numberOperation(Arr::get($data, 'id'), Arr::get($data, 'count_key'), Arr::get($data, 'count_value'));
    }
}
