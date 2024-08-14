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
use App\Vo\QueueMessageVo;
use Hyperf\Amqp\Message\ProducerMessageInterface;
use Hyperf\Amqp\Producer;
use Hyperf\Codec\Json;
use Throwable;

use function Hyperf\Config\config;

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

    public function pushMessage(ProducerMessageInterface $producer, QueueMessageVo $messageVo): bool
    {
        if (! config('amqp.enable')) {
            throw new BusinessException(ErrorCode::QUEUE_NOT_ENABLE);
        }
        if (empty($messageVo->getTitle()) || empty($messageVo->getContent()) || empty($messageVo->getContentType())) {
            throw new BusinessException(ErrorCode::QUEUE_MISSING_MESSAGE);
        }
        $data = array_merge($messageVo->toMap());
        $producer->setPayload($data);
        $queueName = strchr($producer->getRoutingKey(), '.', true) . '.queue';
        $id = $this->save([
            'exchange_name' => $producer->getExchange(),
            'routing_key_name' => $producer->getRoutingKey(),
            'queue_name' => $queueName,
            'queue_content' => $producer->payload(),
            'delay_time' => $messageVo->getDelayTime() ?? 0,
            'produce_status' => ProduceStatusCode::PRODUCE_STATUS_WAITING->value,
        ]);
        $payload = Json::decode($producer->payload());
        $producer->setPayload([
            'queue_id' => $id, 'data' => $payload,
        ]);
        if ($messageVo->getDelayTime() > 0 && method_exists($producer, 'setDelayMs')) {
            $producer->setDelayMs($messageVo->getDelayTime() * 1000);
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
