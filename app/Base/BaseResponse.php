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

use App\Constants\ErrorCode;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;

use function Hyperf\Config\config;

class BaseResponse extends Response
{
    public const OK = 0;

    public const ERROR = 500;

    public function getResponse(): ResponsePlusInterface
    {
        return parent::getResponse();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function success(array|object $data = []): ResponseInterface
    {
        $format = [
            /**
             * 'requestId' => RequestIdHolder？,
             * 请求ID（有需求）.
             */
            'path' => di()->get(BaseRequest::class)->getUri()->getPath(),
            'success' => true,
            'code' => self::OK,
            'data' => &$data,
        ];
        $format = $this->toJson($format);
        return $this->handleHeader($this->getResponse())
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fail(mixed $code = self::ERROR, ?string $message = null, array $data = []): ResponseInterface
    {
        $format = [
            /**
             * 'requestId' => RequestIdHolder？,
             * 请求ID（有需求）.
             */
            'path' => di()->get(BaseRequest::class)->getUri()->getPath(),
            'success' => false,
            'code' => $code,
            'message' => $message ?: ErrorCode::SERVER_ERROR->getMessage(),
        ];

        if (! empty($data)) {
            $format['data'] = &$data;
        }

        $format = $this->toJson($format);
        return $this->handleHeader($this->getResponse())
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }

    /**
     * 向浏览器输出图片.
     */
    public function responseImage(string $image, string $type = 'image/png'): ResponseInterface
    {
        return $this->handleHeader($this->getResponse())
            ->withAddedHeader('content-type', $type)
            ->withBody(new SwooleStream($image));
    }

    private function handleHeader(ResponseInterface $response): ResponseInterface
    {
        $headers = config('base-common.http.headers', [
            'Server' => 'web-api',
        ]);
        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }
        return $response;
    }
}
