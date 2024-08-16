<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Controller;

use App\Constants\WsEventCode;
use App\Service\MessageService;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\WebSocketServer\Constant\Opcode;
use Hyperf\WebSocketServer\Context;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use function App\Helper\console;
use function App\Helper\user;

/**
 * Class ServerController.
 */
class WsServerController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    /**
     * 成功连接到 ws 回调.
     * @param Response|Server $server
     * @param Request $request
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onOpen($server, $request): void
    {
        $uid = user()->getUserInfo(
            di()->get(ServerRequestInterface::class)->getQueryParams()['token']
        )['id'];
        Context::set('uid', $uid);

        console()->info(
            "WebSocket [ user connection to message server: id > {$uid}, " .
            "fd > {$request->fd}, time > " . date('Y-m-d H:i:s') . ' ]'
        );
    }

    /**
     * 消息回调.
     * @param Response|Server $server
     * @param Frame $frame
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onMessage($server, $frame): void
    {
        $data = json_decode($frame->data, true);
        switch ($data['event']) {
            case Opcode::PING:
                $server->push(Opcode::PONG);
                break;
            case WsEventCode::GET_UNREAD_MESSAGE->value:
                $service = di()->get(MessageService::class);
                $data = json_encode([
                    'event' => WsEventCode::EV_NEW_MESSAGE->value,
                    'message' => 'success',
                    'data' => $service->getUnreadMessages(Context::get('uid'))['items'],
                ]);
                $server->push($data);
                break;
        }
    }

    /**
     * 关闭 ws 连接回调.
     * @param Response|\Swoole\Server $server
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onClose($server, int $fd, int $reactorId): void
    {
        console()->info(
            'WebSocket [ user close connect for message server: id > ' . Context::get('uid') . ', ' .
            "fd > {$fd}, time > " . date('Y-m-d H:i:s') . ' ]'
        );
    }
}
