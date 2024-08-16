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

use App\Amqp\Consumer\MessageConsumer;
use App\Amqp\Producer\MessageProducer;
use App\Base\BaseService;
use App\Constants\ErrorCode;
use App\Constants\MessageContentTypeCode;
use App\Dao\MessageDao;
use App\Events\PrivateMessageSent;
use App\Exception\BusinessException;
use App\Vo\AmqpQueueVo;
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Event\EventDispatcher;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function App\Helper\user;
use function Hyperf\Config\config;

class MessageService extends BaseService
{
    /**
     * @var MessageDao
     */
    public $dao;

    public function __construct(MessageDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendPrivateMessage(array $params): bool
    {
        if ($params['receive_by'] === user()->getId()) {
            throw new BusinessException(ErrorCode::MESSAGE_CANNOT_SEND_TO_YOURSELF);
        }
        $data = [
            'send_by' => user()->getId(),
            'receive_by' => $params['receive_by'],
            'content' => $params['content'],
            'content_type' => MessageContentTypeCode::TYPE_PRIVATE_MESSAGE->value,
        ];
        /**
         * 使用RabbitMQ异步发送私信会在model save的时候出现 user()->check() 失败. 因为队列的信息是不包含token，也不应该包含token....
         * 所以这里直接使用同步的方式发送私信.
         * 解决了(队列用 insert 不用 save).
         */
        if (config('amqp.enable') && di()->get(MessageConsumer::class)->isEnable()) {
            $data = Arr::merge($data, [
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
                $this->dao->getModel()->getDataScopeField() => user()->getId(),
                'updated_by' => user()->getId(),
            ]);
            $amqpQueueVo = new AmqpQueueVo();
            $amqpQueueVo->setProducer(MessageProducer::class);
            $amqpQueueVo->setData($data);
            if (di()->get(QueueLogService::class)->addQueue($amqpQueueVo)) {
                return true;
            }
        }
        $evDispatcher = di()->get(EventDispatcher::class);
        if ($this->dao->save($data) > 0) {
            $evDispatcher->dispatch(new PrivateMessageSent($data));
            return true;
        }
        return false;
    }

    public function getPrivateConversationInfo($params): array
    {
        if ($params['receive_by'] === user()->getId()) {
            throw new BusinessException(ErrorCode::MESSAGE_CANNOT_SEND_TO_YOURSELF);
        }
        return $this->dao->getPageList([
            'getPrivateConversationInfo' => $params['receive_by'],
            'orderBy' => 'created_at',
            'orderType' => 'desc',
            'select' => ['id', 'content', 'content_type', 'created_at', 'send_by', 'receive_by'],
        ]);
    }

    public function getPrivateConversationList($params): array
    {
        return $this->dao->getPageList([
            'getPrivateConversationList' => true,
            'orderBy' => 'created_at',
            'orderType' => 'desc',
            'select' => ['id', 'content', 'content_type', 'created_at', 'send_by']]);
    }

    /**
     * 获取未读消息.
     */
    public function getUnreadMessages(?int $id = null): array
    {
        $params = [
            'user_id' => ! empty($id) ? $id : user()->getId(),
            'orderBy' => 'created_at',
            'orderType' => 'desc',
            'getUnreadMessages' => true,
            'read_status' => MessageContentTypeCode::STATUS_MESSAGE_UNREAD->value,
        ];
        return $this->dao->getPageList($params, false);
    }

    public function handleData(array $params): array
    {
        return $params;
    }
}
