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
use Hyperf\Contract\ApplicationInterface;
use Hyperf\Di\ClassLoader;
use Swoole\Runtime;

use function Hyperf\Support\swoole_hook_flags;

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

require BASE_PATH . '/vendor/autoload.php';

Runtime::enableCoroutine(true, swoole_hook_flags());

ClassLoader::init();

$container = require BASE_PATH . '/config/container.php';

$container->get(ApplicationInterface::class);
