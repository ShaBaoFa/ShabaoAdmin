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
use App\Model\OperationLog;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class OperationLogDao extends BaseDao
{
    /**
     * @var OperationLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = OperationLog::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'ip'),
            fn (Builder $query, $ip) => $query->where('ip', $ip)
        );

        $query->when(
            Arr::get($params, 'service_name'),
            fn (Builder $query, $serviceName) => $query->where('service_name', 'like', '%' . $serviceName . '%')
        );

        $query->when(
            Arr::get($params, 'username'),
            fn (Builder $query, $username) => $query->where('username', 'like', '%' . $username . '%')
        );

        $query->when(
            $dates = Arr::get($params, 'created_at'),
            function (Builder $query) use ($dates) {
                if (is_array($dates) && count($dates) === 2) {
                    $query->whereBetween('created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            }
        );

        return $query;
    }
}
