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

use App\Constants\ConsumerStatusCode;
use App\Interfaces\QueueLogServiceInterface;
use Exception;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\Inject;
use PhpAmqpLib\Message\AMQPMessage;

class BaseConsumer extends ConsumerMessage
{
    #[Inject]
    protected QueueLogServiceInterface $service;

    public function consumeMessage($data, AMQPMessage $message): Result
    {
        $result = Result::DROP;
        if (empty($data) && ! Arr::accessible($data)) {
            return $result;
        }
        $queueId = Arr::get($data, 'queue_id');
        try {
            $consumeStatus = ['consume_status' => ConsumerStatusCode::CONSUME_STATUS_FAIL->value];
            if ($this->process($this->handleData($data))) {
                Arr::set($consumeStatus, 'consume_status', ConsumerStatusCode::CONSUME_STATUS_SUCCESS->value);
                $result = Result::ACK;
            }
            $this->service->update(
                $queueId,
                $consumeStatus
            );
        } catch (Exception $e) {
            $this->service->update(
                $queueId,
                Arr::merge($consumeStatus, ['log_content' => $e->getMessage()])
            );
        }
        return $result;
    }

    protected function handleData(array $data): array
    {
        //        $queue = $this->service->find(Arr::get($data, 'queue_id'));
        //        $createdAt = $queue->value('created_at');
        //        $createdBy = $queue->value($queue->getDataScopeField());
        return Arr::get($data, 'data');
        //        $data[$this->consumeService->dao->getModel()->getDataScopeField()] = $createdBy;
        //        Arr::set($data, 'created_by', $createdBy);
        //        Arr::set($data, 'created_at', $createdAt);
        //        Arr::set($data, 'updated_by', $createdBy);
        //        Arr::set($data, 'updated_at', $createdAt);
    }

    protected function process(array $data): bool
    {
        return true;
    }
}
