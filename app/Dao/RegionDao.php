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
use Carbon\Carbon;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;

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

    public function regionActive($params): array
    {
        $recent = $this->getRecent($params);
        return $this->model::select([
            'region.id as region_id',
            'region.name as region_name',
            Db::raw('COUNT(DISTINCT organization_user.user_id) as user_count'),
        ])
            ->leftJoin('organizations', 'organizations.province_region_id', '=', 'region.id')
            ->leftJoin('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->leftJoin('users', 'users.id', '=', 'organization_user.user_id')
            ->where('region.level', 1)
            ->where('users.login_time', '>=', $recent)
            ->groupBy('region.id', 'region.name')->orderBy('user_count', 'desc')
            ->get()->toArray();
    }

    private function getRecent($params): Carbon
    {
        if ((int) $recent = Arr::get($params, 'recent')) {
            if ($recent === 7) {
                return Carbon::now()->subWeek();
            }
            if ($recent === 365) {
                return Carbon::now()->subYear();
            }
            if ($recent === 30) {
                return Carbon::now()->subMonth();
            }
        }
        return Carbon::now()->subWeek();
    }
}
