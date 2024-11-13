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

namespace App\Aspect;

use App\Annotation\Captcha;
use App\Base\BaseRequest;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function Hyperf\Config\config;

#[Aspect]
class CaptchaAspect extends AbstractAspect
{
    public array $annotations = [
        Captcha::class,
    ];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint): ResponseInterface
    {
        $request = $this->container->get(BaseRequest::class);
        if (! $this->checkCaptcha((string) $request->input('captcha_key'), (string) $request->input('captcha_code'))) {
            throw new BusinessException(ErrorCode::CAPTCHA_ERROR);
        }
        return $proceedingJoinPoint->process();
    }

    private function checkCaptcha(string $captchaKey, string $code): bool
    {
        $redis = $this->container->get(Redis::class);
        $key = sprintf('%scaptcha:%s', config('cache.default.prefix'), $captchaKey);
        if ($redis->get($key) === $code) {
            $redis->del($key);
            return true;
        };
        return false;
    }
}
