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
use App\Constants\QueueMesContentTypeCode;
use App\Model\Message;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;

use function App\Helper\user;

class MessageDao extends BaseDao
{
    /**
     * @var Message
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Message::class;
    }

    /**
     * 获取私信对话列表(只取当前用户的私信).
     */
    public function getPrivateConversationList(): array
    {
        $id = user()->getId();
        // 第一步：获取步骤1的结果并将其作为子查询
        $subQuery = Db::table('messages')
            ->selectRaw('LEAST(send_by, receive_by) AS user1, GREATEST(send_by, receive_by) AS user2, MAX(created_at) AS last_message_time')
            ->where(function ($query) use ($id) {
                $query->where('send_by', $id)
                    ->orWhere('receive_by', $id);
            })
            ->where('content_type', QueueMesContentTypeCode::TYPE_PRIVATE_MESSAGE->value)
            ->groupBy(Db::raw('LEAST(send_by, receive_by), GREATEST(send_by, receive_by)'));

        // 第二步：使用子查询与原表进行 JOIN
        return Db::table('messages')
            ->joinSub($subQuery, 'sub', function ($join) {
                $join->on(Db::raw('LEAST(messages.send_by, messages.receive_by)'), '=', 'sub.user1')
                    ->on(Db::raw('GREATEST(messages.send_by, messages.receive_by)'), '=', 'sub.user2')
                    ->on('messages.created_at', '=', 'sub.last_message_time');
            })
            ->select('messages.*')
            ->get()->toArray();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'title'),
            fn (Builder $query, $title) => $query->where('title', 'like', '%' . $title . '%')
        );

        $query->when(
            Arr::get($params, 'content_type'),
            function (Builder $query, $contentType) {
                if ($contentType !== 'all') {
                    $query->where('content_type', '=', $contentType);
                }
            }
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
