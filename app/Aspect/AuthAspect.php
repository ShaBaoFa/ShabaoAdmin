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
use App\Constants\AuthGuardType;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Psr\Container\ContainerInterface;

#[Aspect]
class AuthAspect extends AbstractAspect
{
    public array $annotations = [
        Auth::class,
    ];

    public function __construct(protected ContainerInterface $container)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $guard = 'jwt';

        /* @var $auth Auth */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->class[Auth::class])) {
            $auth = $proceedingJoinPoint->getAnnotationMetadata()->class[Auth::class];
            $guard = $auth->guard ?? 'jwt';
        }

        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class])) {
            $auth = $proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class];
            $guard = $auth->guard ?? 'default';
        }
        $guard = AuthGuardType::from($guard);
        $currentUser = user($guard);

        $currentUser->check();

        return $proceedingJoinPoint->process();
    }
}
