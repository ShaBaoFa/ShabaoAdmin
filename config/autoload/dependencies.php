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
use App\Kernel\Event\EventDispatcherFactory;
use App\Kernel\Http\WorkerStartListener;
use App\Kernel\Log\LoggerFactory;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Server\Listener\AfterWorkerStartListener;
use Psr\EventDispatcher\EventDispatcherInterface;

return [
    StdoutLoggerInterface::class => LoggerFactory::class,
    AfterWorkerStartListener::class => WorkerStartListener::class,
    EventDispatcherInterface::class => EventDispatcherFactory::class,
];
