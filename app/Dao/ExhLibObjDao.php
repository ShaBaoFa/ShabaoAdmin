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
        $obj->tags()->sync($tags);
        $obj->files()->sync($files);
        $obj->covers()->sync($covers);
        $obj->share_regions()->sync($share_regions);
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
        $model->tags()->sync($tags);
        $model->files()->sync($files);
        $model->covers()->sync($covers);
        $model->share_regions()->sync($share_regions);
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
}
