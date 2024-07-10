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

namespace App\Dao;

use App\Base\BaseDao;
use App\Model\LoginLog;

class LoginLogDao extends BaseDao
{
    /**
     * @var LoginLog
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = LoginLog::class;
    }
}
