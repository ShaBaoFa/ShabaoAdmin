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
use App\Constants\AuditCode;
use App\Model\Announcement;
use App\Model\Comment;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class CommentDao extends BaseDao
{
    /**
     * @var Comment
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Comment::class;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            ! is_null(Arr::get($params, 'parent_id')),
            fn (Builder $query) => $query->where('parent_id', Arr::get($params, 'parent_id'))
        );
        $query->when(
            Arr::get($params, 'commentable_id'),
            fn (Builder $query, $commentableId) => $query->where('commentable_id', $commentableId)
        );
        $query->when(
            Arr::get($params, 'commentable_type'),
            fn (Builder $query, $commentableType) => $query->where('commentable_type', $commentableType)
        );


        $query->when(
            Arr::get($params, 'created_at'),
            function (Builder $query, $publishedAt) {
                if (is_array($publishedAt) && count($publishedAt) === 2) {
                    $query->whereBetween(
                        'created_at',
                        [$publishedAt[0] . ' 00:00:00', $publishedAt[1] . ' 23:59:59']
                    );
                }
            }
        );

        return $query;
    }

    public function addStar(int $id): bool
    {
        $model = $this->find($id);
        $model->increment('star_count');
        return true;
    }

    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }
}
