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
use Hyperf\Database\Model\Builder;

use function App\Helper\filled;

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
        // 交换机名称
        if (isset($params['exchange_name']) && filled($params['exchange_name'])) {
            $query->where('exchange_name', '=', $params['exchange_name']);
        }

        // 路由名称
        if (isset($params['routing_key_name']) && filled($params['routing_key_name'])) {
            $query->where('routing_key_name', '=', $params['routing_key_name']);
        }

        // 队列名称
        if (isset($params['queue_name']) && filled($params['queue_name'])) {
            $query->where('queue_name', '=', $params['queue_name']);
        }

        // 生产状态 1:未生产 2:生产中 3:生产成功 4:生产失败 5:生产重复
        if (isset($params['produce_status']) && filled($params['produce_status'])) {
            $query->where('produce_status', '=', $params['produce_status']);
        }

        // 消费状态 1:未消费 2:消费中 3:消费成功 4:消费失败 5:消费重复
        if (isset($params['consume_status']) && filled($params['consume_status'])) {
            $query->where('consume_status', '=', $params['consume_status']);
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }
        return $query;
    }
}
