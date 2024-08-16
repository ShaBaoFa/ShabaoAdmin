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

namespace App\Middleware;

use App\Constants\ErrorCode;
use Exception;
use Hyperf\Collection\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function App\Helper\user;

class WsAuthMiddleware implements MiddlewareInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = Arr::get($request->getQueryParams(),'token');
        try {
            if ($token && user()->check($token)) {
                return $handler->handle($request);
            }
            return di()->get(\Hyperf\HttpServer\Contract\ResponseInterface::class)->raw(ErrorCode::NO_LOGIN_USER->getMessage());
        } catch (Exception $e) {
            return di()->get(\Hyperf\HttpServer\Contract\ResponseInterface::class)->raw($e->getMessage());
        }
    }
}
