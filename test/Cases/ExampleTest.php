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

namespace HyperfTest\Cases;

use App\Kernel\Context\Coroutine;
use App\Kernel\Log\AppendRequestIdProcessor;
use Hyperf\Context\Context;
use Hyperf\Di\Definition\FactoryDefinition;
use Hyperf\Di\Resolver\FactoryResolver;
use Hyperf\Di\Resolver\ResolverDispatcher;
use Hyperf\Engine\Channel;
use Hyperf\Support\Reflection\ClassInvoker;
use HyperfTest\HttpTestCase;
use Mockery;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends HttpTestCase
{
    public function testExample()
    {
        $this->assertTrue(true);

    }
}
