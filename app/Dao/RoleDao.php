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
use App\Model\Role;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;

use function Hyperf\Support\env;

class RoleDao extends BaseDao
{
    /**
     * @var Role
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Role::class;
    }

    /**
     * 通过 code 查询角色名称.
     */
    public function findNameByCode(string $code): ?string
    {
        return $this->model::query()->where('code', $code)->value('name');
    }

    /**
     * 检查角色code是否已存在.
     */
    public function checkRoleCode(string $code): bool
    {
        return $this->model::query()->where('code', $code)->exists();
    }

    /**
     * 通过角色ID列表获取菜单ID.
     */
    public function getMenuIdsByRoleIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->model::query()->whereIn('id', $ids)->with(['menus' => function ($query) {
            $query->select('id')->where('status', $this->model::ENABLE)->orderBy('sort', 'desc');
        }])->get(['id'])->toArray();
    }

    /**
     * 通过角色ID列表获取部门ID.
     */
    public function getDeptIdsByRoleIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->model::query()->whereIn('id', $ids)->with(['depts' => function ($query) {
            $query->select('id')->where('status', $this->model::ENABLE)->orderBy('sort', 'desc');
        }])->get(['id'])->toArray();
    }

    /**
     * 新建角色.
     */
    #[Transactional]
    public function save(array $data): mixed
    {
        $menuIds = $data['menu_ids'] ?? [];
        $deptIds = $data['dept_ids'] ?? [];
        $role = $this->model::create($data);
        empty($menuIds) || $role->menus()->sync(array_unique($menuIds), false);
        empty($deptIds) || $role->depts()->sync($deptIds, false);
        return $role->id;
    }

    /**
     * 更新角色.
     */
    #[CacheEvict(prefix: 'LoginInfo', all: true), Transactional]
    public function update(mixed $id, array $data): bool
    {
        $menuIds = $data['menu_ids'] ?? [];
        $deptIds = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);
        $this->model::query()->where('id', $id)->update($data);
        if ($id != env('ADMIN_ROLE')) {
            $role = $this->model::find($id);
            if ($role) {
                ! empty($menuIds) && $role->menus()->sync(array_unique($menuIds));
                ! empty($deptIds) && $role->depts()->sync($deptIds);
                return true;
            }
        }
        return false;
    }

    /**
     * 批量真实删除角色.
     */
    #[CacheEvict(prefix: 'LoginInfo', all: true), Transactional]
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            if ($id == env('ADMIN_ROLE')) {
                continue;
            }
            $role = $this->model::withTrashed()->find($id);
            if ($role) {
                // 删除关联菜单
                $role->menus()->detach();
                // 删除关联部门
                $role->depts()->detach();
                // 删除关联用户
                $role->users()->detach();
                // 删除角色数据
                $role->forceDelete();
            }
        }
        return true;
    }

    /**
     * 单个或批量软删除数据.
     */
    #[CacheEvict(prefix: 'LoginInfo', all: true)]
    public function delete(array $ids): bool
    {
        $adminId = env('ADMIN_ROLE');
        if (in_array($adminId, $ids)) {
            unset($ids[array_search($adminId, $ids)]);
        }
        $this->model::destroy($ids);
        return true;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'name'),
            fn (Builder $query, $name) => $query->where('name', 'like', '%' . $name . '%')
        );

        $query->when(
            Arr::get($params, 'code'),
            fn (Builder $query, $code) => $query->where('code', $code)
        );

        $query->when(
            Arr::get($params, 'status'),
            fn (Builder $query, $status) => $query->where('status', $status)
        );

        $query->when(
            Arr::get($params, 'filterAdminRole'),
            fn (Builder $query) => $query->whereNotIn('id', [env('ADMIN_ROLE')])
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
