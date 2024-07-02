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

use App\Model\Model;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Psr\Container\ContainerInterface;

#[Aspect]
class ModelSaveAspect extends AbstractAspect
{
    public array $classes = [
        'App\Model\Model::save',
    ];

    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * @throws Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $instance = $proceedingJoinPoint->getInstance();

        // 获取当前登录用户信息
        // 设置创建人
        if ($instance instanceof Model && in_array($instance->getDataScopeField(), $instance->getFillable()) && is_null($instance[$instance->getDataScopeField()])) {
            $user = user();
            $user->check();
            $instance[$instance->getDataScopeField()] = $user->getId();
        }

        // 设置更新人
        if ($instance instanceof Model && in_array('updated_by', $instance->getFillable())) {
            $user = user();
            $user->check();
            $instance->updated_by = $user->getId();
        }

        return $proceedingJoinPoint->process();
    }
}
