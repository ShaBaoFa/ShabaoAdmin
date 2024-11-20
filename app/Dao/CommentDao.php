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
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\Comment;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

use function App\Helper\user;

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
        /**
         * @var Comment $model
         */
        $userId = user()->getId();
        $model = $this->find($id);
        // 检查用户是否已经点赞
        if ($model->starUsers()->where('user_id', $userId)->exists()) {
            throw new BusinessException(ErrorCode::FORBIDDEN);
        }

        // 添加点赞关系
        $model->starUsers()->attach($userId);

        // 如果需要，更新 star_count 字段
        $model->increment('star_count');

        return true;
    }

    public function cancelStar(int $id): bool
    {
        /**
         * @var Comment $model
         */
        $userId = user()->getId();
        $model = $this->find($id);
        // 检查用户是否已经点赞
        if ($model->starUsers()->where('user_id', $userId)->doesntExist()) {
            throw new BusinessException(ErrorCode::FORBIDDEN);
        }

        // 添加点赞关系
        $model->starUsers()->detach($userId);

        // 如果需要，更新 star_count 字段
        $model->decrement('star_count');

        return true;
    }

    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }
}
