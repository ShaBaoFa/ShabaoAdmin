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

namespace App\Controller;

use App\Annotation\Auth;
use App\Annotation\Permission;
use App\Base\BaseController;
use App\Request\MessageRequest;
use App\Service\MessageService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/messages'),Auth]
class MessageController extends BaseController
{
    #[Inject]
    protected MessageService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('sendPrivateMessage')]
    public function sendPrivateMessage(MessageRequest $request): ResponseInterface
    {
        return $this->service->sendPrivateMessage($request->all()) ? $this->response->success() : $this->response->fail();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getPrivateConversationInfo'), Permission('messages:get_private_conversation_info')]
    public function getPrivateConversationInfo(MessageRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getPrivateConversationInfo($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getPrivateConversationList'), Permission('messages:get_private_conversation_list')]
    public function getPrivateConversationList(MessageRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getPrivateConversationList($request->all()));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getUnreadMessages'), Permission('messages:get_unread_messages')]
    public function getUnreadMessages(MessageRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->getUnreadMessages());
    }
}
