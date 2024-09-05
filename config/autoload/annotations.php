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
use Hyperf\Coroutine\Coroutine;
use Hyperf\Di\Resolver\ResolverDispatcher;

return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
        ],
        'ignore_annotations' => [
            'mixin',
        ],
        'class_map' => [
            Coroutine::class => BASE_PATH . '/storage/classes/Coroutine.php',
            ResolverDispatcher::class => BASE_PATH . '/storage/classes/ResolverDispatcher.php',
        ],
    ],
];
