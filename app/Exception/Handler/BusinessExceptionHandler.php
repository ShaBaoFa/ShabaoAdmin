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

namespace App\Exception\Handler;

use App\Base\BaseResponse;
use App\Constants\ErrorCode;
use App\Exception\AuthException;
use App\Exception\BusinessException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Exception\CircularDependencyException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class BusinessExceptionHandler extends ExceptionHandler
{
    protected BaseResponse $response;

    protected LoggerInterface $logger;

    public function __construct(protected ContainerInterface $container)
    {
        $this->response = $container->get(BaseResponse::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        switch (true) {
            case $throwable instanceof ValidationException:
                return $this->response->fail(ErrorCode::INVALID_PARAMS->value, $throwable->validator->errors()->first())->withStatus(ErrorCode::INVALID_PARAMS->value);
            case $throwable instanceof AuthException:
                $this->logger->warning(format_throwable($throwable));
                return $this->response->fail(ErrorCode::UNAUTHORIZED->value, ErrorCode::UNAUTHORIZED->getMessage())->withStatus(ErrorCode::UNAUTHORIZED->value);
            case $throwable instanceof HttpException:
                return $this->response->handleException($throwable);
            case $throwable instanceof BusinessException:
                $this->logger->warning(format_throwable($throwable));
                return $this->response->fail($throwable->getCode(), $throwable->getMessage());
            case $throwable instanceof CircularDependencyException:
                $this->logger->error($throwable->getMessage());
                return $this->response->fail(ErrorCode::SERVER_ERROR->value, $throwable->getMessage());
        }

        $this->logger->error(format_throwable($throwable));

        return $this->response->fail(ErrorCode::SERVER_ERROR->value, ErrorCode::SERVER_ERROR->getMessage());
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
