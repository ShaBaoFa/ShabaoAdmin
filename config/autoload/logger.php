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
use App\Log\Processor\UuidRequestIdProcessor;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

if (\Hyperf\Support\env('APP_ENV') === 'testing') {
    $handler = [
        'class' => StreamHandler::class,
        'constructor' => [
            'stream' => 'php://output',
            'level' => Level::Debug,
        ],
    ];
}

return [
    'default' => [
        'handlers' => ['biz'],
    ],
    'biz' => [
        'handler' => $handler ?? [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                //                'stream' => 'php://stdout',
                'filename' => BASE_PATH . '/runtime/logs/hyperf-error.log',
                'level' => Level::Error,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
        'processors' => [
            [
                'class' => UuidRequestIdProcessor::class,
            ],
        ],
    ],
    'sql' => [
        'handler' => $handler ?? [
            'class' => RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/sql/sql.log',
                'level' => Level::Info,
            ],
        ],
        'formatter' => [
            'class' => LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
        'processor' => [
            'class' => UuidRequestIdProcessor::class,
        ],
    ],
];
