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
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

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

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'status'),
            fn (Builder $query, $status) => $query->where('status', $status)
        );
        $query->when(
            Arr::get($params, 'ip'),
            fn (Builder $query, $ip) => $query->where('ip', $ip)
        );
        $query->when(
            Arr::get($params, 'username'),
            fn (Builder $query, $username) => $query->where('username', $username)
        );

        $query->when(
            Arr::get($params, 'login_time'),
            function (Builder $query, $loginTime) {
                if (is_array($loginTime) && count($loginTime) === 2) {
                    $query->whereBetween(
                        'login_time',
                        [$loginTime[0] . ' 00:00:00', $loginTime[1] . ' 23:59:59']
                    );
                }
            }
        );

        return $query;
    }
}
