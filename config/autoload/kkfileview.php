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
use function Hyperf\Support\env;

return [
    'base_uri' => env('KK_FILE_VIEW_BASE_URI', 'http://127.0.0.1:8012'), //  kkfileview base_uri
    'timeout' => env('KK_FILE_VIEW_TIMEOUT', 5), //  kkfileview timeout
];
