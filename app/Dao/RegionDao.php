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
use App\Model\Region;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class RegionDao extends BaseDao
{
    /**
     * @var Region
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Region::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            ! is_null($level = Arr::get($params, 'level')),
            fn (Builder $query) => $query->where('level', $level)
        );

        $query->when(
            ! is_null($parentId = Arr::get($params, 'parent_id')),
            fn (Builder $query) => $query->where('parent_id', $parentId)
        );

        $query->when(
            $keywords = Arr::get($params, 'keywords'),
            fn (Builder $query) => $query->where(function (Builder $builder) use ($keywords) {
                $builder->where('name', 'like', '%' . $keywords . '%')
                    ->orWhere('initial', 'like', '%' . $keywords . '%')
                    ->orWhere('pinyin', 'like', '%' . $keywords . '%');
            })
        );

        return $query;
    }
}
