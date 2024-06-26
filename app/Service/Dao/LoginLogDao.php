<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service\Dao;

use App\Model\LoginLog;

class LoginLogDao extends BaseDao
{
    public function save(array $input): void
    {
        // Save login log.
        $log = new LoginLog();
        $log->username = $input['username'];
        $log->ip = $input['ip'];
        $log->ip_location = $input['ip_location'];
        $log->os = $input['os'];
        $log->browser = $input['browser'];
        $log->status = $input['status'];
        $log->message = $input['message'];
        $log->login_time = date('Y-m-d H:i:s');
        $log->save();
    }
}
