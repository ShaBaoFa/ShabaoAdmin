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

namespace App\Dao;

use App\Model\LoginLog;

class LoginLogDao extends BaseDao
{
    public $model;

    public function assignModel(): void
    {
        $this->model = LoginLog::class;
    }
}
