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

namespace App\Listener;

use App\Constants\WsEventCode;
use App\Events\AfterKickUser;
use App\Events\PrivateMessageSent;
use App\Model\User;
use App\Service\WsSenderService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

#[Listener]
class WsSenderListener implements ListenerInterface
{
    private WsSenderService $sender;

    public function __construct(protected ContainerInterface $container)
    {
        $this->sender = $container->get(WsSenderService::class);
    }

    public function listen(): array
    {
        return [
            PrivateMessageSent::class,
            AfterKickUser::class,
        ];
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     */
    public function process(object $event): void
    {
        if ($event instanceof PrivateMessageSent) {
            $uid = $event->getReceiveBy();
            $sendBy = $event->getSendBy();
            $sendByUsername = User::find($event->getSendBy())->value('username');
            $content = $event->getContent();
            $this->sender->sendByUid($uid, $this->sender->handleData(WsEventCode::EV_NEW_PRIVATE_MESSAGE, [
                'send_by' => $sendBy,
                'send_by_username' => $sendByUsername,
                'content' => $content,
            ]));
        }
        if ($event instanceof AfterKickUser) {
            $uid = $event->getUid();
            $this->sender->kickAndSendByUid($uid, $this->sender->handleData(WsEventCode::EV_USER_KICK_OUT, []));
        }
    }
}
