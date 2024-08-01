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
use Monolog\Level;

return [
    'default' => [
        'handlers' => [
            [
                'class' => RotatingFileHandler::class,
                'constructor' => [
                    //                'stream' => 'php://stdout',
                    'filename' => BASE_PATH . '/runtime/logs/hyperf-info.log',
                    'level' => Level::Info,
                ],
            ],
            [
                'class' => RotatingFileHandler::class,
                'constructor' => [
                    //                'stream' => 'php://stdout',
                    'filename' => BASE_PATH . '/runtime/logs/hyperf-debug.log',
                    'level' => Level::Debug,
                ],
            ],
            [
                'class' => RotatingFileHandler::class,
                'constructor' => [
                    //                'stream' => 'php://stdout',
                    'filename' => BASE_PATH . '/runtime/logs/hyperf-warning.log',
                    'level' => Level::Warning,
                ],
            ],
            [
                'class' => RotatingFileHandler::class,
                'constructor' => [
                    //                'stream' => 'php://stdout',
                    'filename' => BASE_PATH . '/runtime/logs/hyperf-error.log',
                    'level' => Level::Error,
                ],
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
        'handler' => [
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
