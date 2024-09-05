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

use App\Base\BaseModel;
use App\Base\BaseRequest;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function App\Helper\user;
use function Hyperf\Config\config;

#[Aspect]
class ModelUpdateAspect extends AbstractAspect
{
    public array $classes = [
        'App\Base\BaseModel::save',
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
        $instance = $proceedingJoinPoint->getInstance();
        // 更新更改人
        if ($instance instanceof BaseModel
            && in_array('updated_by', $instance->getFillable())
            && config('base-common.data_scope_enabled')
            && Context::has(ServerRequestInterface::class)
            && di()->get(BaseRequest::class)->getHeaderLine('authorization')
        ) {
            try {
                $instance->updated_by = user()->getId() ?? 0;
            } catch (Throwable $e) {
            }
        }
        return $proceedingJoinPoint->process();
    }
}
