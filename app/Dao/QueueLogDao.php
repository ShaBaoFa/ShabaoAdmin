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
use App\Model\QueueLog;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class QueueLogDao extends BaseDao
{
    /**
     * @var QueueLog
     */
    public $model;

    public function assignModel()
    {
        $this->model = QueueLog::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'exchange_name'),
            fn (Builder $query, $exchangeName) => $query->where('exchange_name', '=', $exchangeName)
        );

        $query->when(
            Arr::get($params, 'routing_key_name'),
            fn (Builder $query, $routingKeyName) => $query->where('routing_key_name', '=', $routingKeyName)
        );

        $query->when(
            Arr::get($params, 'queue_name'),
            fn (Builder $query, $queueName) => $query->where('queue_name', '=', $queueName)
        );

        $query->when(
            Arr::get($params, 'produce_status'),
            fn (Builder $query, $produceStatus) => $query->where('produce_status', '=', $produceStatus)
        );

        $query->when(
            Arr::get($params, 'consume_status'),
            fn (Builder $query, $consumeStatus) => $query->where('consume_status', '=', $consumeStatus)
        );

        $query->when(
            Arr::get($params, 'created_at'),
            function (Builder $query, $createdAt) {
                if (is_array($createdAt) && count($createdAt) === 2) {
                    $query->whereBetween(
                        'created_at',
                        [$createdAt[0] . ' 00:00:00', $createdAt[1] . ' 23:59:59']
                    );
                }
            }
        );

        return $query;
    }
}
