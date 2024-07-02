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

namespace App\Kernel\Http;

use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\Server\Listener\AfterWorkerStartListener;
use Psr\Container\ContainerInterface;

class WorkerStartListener extends AfterWorkerStartListener
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container->get(StdoutLogger::class));
    }
}
