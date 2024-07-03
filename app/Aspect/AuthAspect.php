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

use App\Annotation\Auth;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Aspect]
class AuthAspect extends AbstractAspect
{
    public array $annotations = [
        Auth::class,
    ];

    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $scene = 'default';

        /* @var $auth Auth */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->class[Auth::class])) {
            $auth = $proceedingJoinPoint->getAnnotationMetadata()->class[Auth::class];
            $scene = $auth->scene ?? 'default';
        }

        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class])) {
            $auth = $proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class];
            $scene = $auth->scene ?? 'default';
        }
        $currentUser = user($scene);

        $currentUser->check(scene:$scene);

        return $proceedingJoinPoint->process();
    }
}
