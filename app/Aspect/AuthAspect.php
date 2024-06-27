<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
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
