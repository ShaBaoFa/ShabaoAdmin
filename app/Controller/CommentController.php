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
use App\Base\BaseController;
use App\Request\CommentRequest;
use App\Service\CommentService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'api/v1/comment'),Auth]
class CommentController extends BaseController
{
    #[Inject]
    protected CommentService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('save')]
    public function save(CommentRequest $request): ResponseInterface
    {
        return $this->response->success(['id' => $this->service->save($request->all())]);
    }

    #[GetMapping('index')]
    public function index(CommentRequest $request): ResponseInterface
    {
        return $this->response->success($this->service->index($request->all()));
    }

    #[PostMapping('addStar')]
    public function addStar(CommentRequest $request): ResponseInterface
    {
        return $this->service->addStar((int) $request->input('id')) ? $this->response->success() : $this->response->fail();
    }

    #[PostMapping('cancelStar')]
    public function cancelStar(CommentRequest $request): ResponseInterface
    {
        return $this->service->cancelStar((int) $request->input('id')) ? $this->response->success() : $this->response->fail();
    }
}
