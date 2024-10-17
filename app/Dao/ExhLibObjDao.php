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
use App\Model\ExhLibObj;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;

use function App\Helper\user;

class ExhLibObjDao extends BaseDao
{
    /**
     * @var ExhLibObj
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = ExhLibObj::class;
    }

    #[Transactional]
    public function save(array $data): mixed
    {
        $tags = Arr::has($data, 'tags') ? Arr::get($data, 'tags') : [];
        $files = Arr::has($data, 'files') ? Arr::get($data, 'files') : [];
        $covers = Arr::has($data, 'covers') ? Arr::get($data, 'covers') : [];
        $share_regions = Arr::has($data, 'share_regions') ? Arr::get($data, 'share_regions') : [];
        $this->filterExecuteAttributes($data, true);
        $obj = $this->model::create($data);
        ! empty($tags) ?? $obj->tags()->sync($tags);
        ! empty($files) ?? $obj->files()->sync($files);
        ! empty($covers) ?? $obj->covers()->sync($covers);
        ! empty($share_regions) ?? $obj->share_regions()->sync($share_regions);
        return $obj->{$obj->getKeyName()};
    }

    #[Transactional]
    public function update(mixed $id, array $data): bool
    {
        $tags = Arr::has($data, 'tags') ? Arr::get($data, 'tags') : [];
        $files = Arr::has($data, 'files') ? Arr::get($data, 'files') : [];
        $covers = Arr::has($data, 'covers') ? Arr::get($data, 'covers') : [];
        $share_regions = Arr::has($data, 'share_regions') ? Arr::get($data, 'share_regions') : [];
        $this->filterExecuteAttributes($data, true);
        $model = $this->model::find($id);
        ! empty($tags) ?? $model->tags()->sync($tags);
        ! empty($files) ?? $model->files()->sync($files);
        ! empty($covers) ?? $model->covers()->sync($covers);
        ! empty($share_regions) ?? $model->share_regions()->sync($share_regions);
        return parent::update($id, $data);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'type'),
            fn (Builder $query, $type) => $query->where('type', $type)
        );

        $query->when(
            Arr::get($params, 'status'),
            fn (Builder $query, $status) => $query->where('status', $status)
        );

        $query->when(
            Arr::get($params, 'audit_status'),
            fn (Builder $query, $auditStatus) => $query->where('audit_status', $auditStatus)
        );

        $query->when(
            Arr::get($params, 'lib_type'),
            fn (Builder $query, $libType) => $query->where('lib_type', $libType)
        );

        $query->when(
            Arr::get($params, 'title'),
            fn (Builder $query, $title) => $query->where('title', 'like', '%' . $title . '%')
        );

        $query->when(
            Arr::get($params, 'lib_area_type'),
            fn (Builder $query, $libAreaType) => $query->where('lib_area_type', $libAreaType)
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

    public function addStar(int $id): bool
    {
        $model = $this->find($id);
        $model->increment('star_count');
        /**
         * @var ExhLibObj $model
         */
        $result = $model->starUsers()->syncWithoutDetaching([user()->getId()]);
        return count($result['attached']) > 0;
    }

    public function cancelStar(int $id): bool
    {
        $model = $this->find($id);
        $model->decrement('star_count');
        /**
         * @var ExhLibObj $model
         */
        return $model->starUsers()->detach([user()->getId()], false) > 0;
    }

    public function addCollection(int $id): bool
    {
        $model = $this->find($id);
        $model->increment('collect_count');
        /**
         * @var ExhLibObj $model
         */
        $result = $model->collectUsers()->syncWithoutDetaching([user()->getId()]);
        return count($result['attached']) > 0;
    }

    public function cancelCollection(int $id): bool
    {
        $model = $this->find($id);
        $model->decrement('collect_count');
        /**
         * @var ExhLibObj $model
         */
        return $model->collectUsers()->detach([user()->getId()], false) > 0;
    }

    public function addPick(int $id): bool
    {
        $model = $this->find($id);
        /**
         * @var ExhLibObj $model
         */
        $result = $model->pickUsers()->syncWithoutDetaching([user()->getId()]);
        return count($result['attached']) > 0;
    }

    public function cancelPick(int $id): bool
    {
        $model = $this->find($id);
        /**
         * @var ExhLibObj $model
         */
        return $model->pickUsers()->detach([user()->getId()], false) > 0;
    }

    public function hasStarred(int $id, int $userId): bool
    {
        $model = $this->model::find($id);
        /**
         * @var ExhLibObj $model
         */
        return $model->starUsers()->where('user_id', $userId)->exists();
    }

    public function hasPicked(int $id, int $userId): bool
    {
        $model = $this->model::find($id);
        /**
         * @var ExhLibObj $model
         */
        return $model->pickUsers()->where('user_id', $userId)->exists();
    }

    public function hasCollected(int $id, int $userId): bool
    {
        $model = $this->model::find($id);
        /**
         * @var ExhLibObj $model
         */
        return $model->collectUsers()->where('user_id', $userId)->exists();
    }
}
