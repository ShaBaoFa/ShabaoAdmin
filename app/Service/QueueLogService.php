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

namespace App\Service;

use App\Base\BaseService;
use App\Constants\ErrorCode;
use App\Constants\ProduceStatusCode;
use App\Dao\QueueLogDao;
use App\Exception\BusinessException;
use App\Interfaces\QueueLogServiceInterface;
use App\Vo\AmqpQueueVo;
use Hyperf\Amqp\Producer;
use Hyperf\Codec\Json;
use Throwable;

use function Hyperf\Config\config;
use function Hyperf\Support\make;

class QueueLogService extends BaseService implements QueueLogServiceInterface
{
    /**
     * @var QueueLogDao
     */
    public $dao;

    /**
     * SystemQueueLogService constructor.
     */
    public function __construct(
        QueueLogDao $dao,
        protected readonly Producer $producer,
    ) {
        $this->dao = $dao;
    }

    public function addQueue(AmqpQueueVo $amqpQueueVo): bool
    {
        if (! config('amqp.enable') || ! class_exists($amqpQueueVo->getProducer())) {
            throw new BusinessException(ErrorCode::QUEUE_NOT_ENABLE);
        }
        if (empty($amqpQueueVo->getData())) {
            throw new BusinessException(ErrorCode::QUEUE_MISSING_MESSAGE);
        }
        $class = $amqpQueueVo->getProducer();
        // 通过反射获取实例
        $producer = make($class, [$amqpQueueVo->getData()]);
        $queueName = strchr($producer->getRoutingKey(), '.', true) . '.queue';
        $id = $this->save([
            'exchange_name' => $producer->getExchange(),
            'routing_key_name' => $producer->getRoutingKey(),
            'queue_name' => $queueName,
            'queue_content' => $producer->payload(),
            'delay_time' => $amqpQueueVo->getDelayTime() ?? 0,
            'produce_status' => ProduceStatusCode::PRODUCE_STATUS_WAITING->value,
        ]);
        $payload = Json::decode($producer->payload());
        $producer->setPayload([
            'queue_id' => $id, 'data' => $payload,
        ]);
        if ($amqpQueueVo->getDelayTime() > 0 && method_exists($producer, 'setDelayMs')) {
            $producer->setDelayMs($amqpQueueVo->getDelayTime() * 1000);
        }
        try {
            $result = $this->producer->produce($producer);
        } catch (Throwable $e) {
            $this->update((int) $id, ['produce_status' => ProduceStatusCode::PRODUCE_STATUS_FAIL->value, 'log_content' => $e->getMessage()]);
            return false;
        }
        $result ? $this->update((int) $id, ['produce_status' => ProduceStatusCode::PRODUCE_STATUS_SUCCESS->value]) : $this->update((int) $id, ['produce_status' => ProduceStatusCode::PRODUCE_STATUS_FAIL->value]);
        return $result;
    }
}
