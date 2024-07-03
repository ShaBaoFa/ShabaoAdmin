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

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class BaseController
{
    protected BaseResponse $response;

    protected BaseRequest $request;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(protected ContainerInterface $container)
    {
        $this->response = $container->get(BaseResponse::class);
        $this->request = $container->get(BaseRequest::class);
    }

    public function getResponse(): BaseResponse
    {
        return $this->response;
    }

    public function getRequest(): BaseRequest
    {
        return $this->request;
    }
}
