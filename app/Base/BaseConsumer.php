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

use App\Interfaces\QueueLogServiceInterface;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Collection\Arr;

class BaseConsumer extends ConsumerMessage
{
    public function __construct(
        private readonly QueueLogServiceInterface $service,
        private readonly BaseService $consumeService
    ) {
    }

    protected function handleData(array $data): array
    {
        $queue = $this->service->find(Arr::get($data, 'queue_id'));
        $createdAt = $queue->value('created_at');
        $createdBy = $queue->value($queue->getDataScopeField());
        $data = Arr::get($data, 'data');
        $data[$this->consumeService->dao->getModel()->getDataScopeField()] = $createdBy;
        Arr::set($data, 'created_by', $createdBy);
        Arr::set($data, 'created_at', $createdAt);
        Arr::set($data, 'updated_by', $createdBy);
        Arr::set($data, 'updated_at', $createdAt);
        return $data;
    }
}
