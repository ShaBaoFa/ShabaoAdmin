<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Log\Processor;

use Hyperf\Coroutine\Coroutine;
use App\Log\RequestIdHolder;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class UuidRequestIdProcessor implements ProcessorInterface
{
    public function __invoke(array|LogRecord $record): array|LogRecord
    {
        RequestIdHolder::setType('uuid');
        $record['extra']['request_id'] = RequestIdHolder::getId();
        $record['extra']['coroutine_id'] = Coroutine::id();
        return $record;
    }
}
