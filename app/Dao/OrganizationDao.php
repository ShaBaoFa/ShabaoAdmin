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

use App\Base\BaseCollection;
use App\Base\BaseDao;
use App\Model\Organization;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\Stringable\Str;
use JetBrains\PhpStorm\ArrayShape;

use function Hyperf\Config\config;

class OrganizationDao extends BaseDao
{
    /**
     * @var Organization
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Organization::class;
    }

    /**
     * 新增组织.
     */
    #[Transactional]
    public function save(array $data): mixed
    {
        $id = parent::save($data);
        $this->genSuperAdmin($id, $data);
        return $id;
    }

    /**
     * 批量更新.
     */
    #[Transactional]
    public function batchUpdate(array $update): bool
    {
        foreach ($update as $item) {
            $result = parent::update($item['id'], $item['data']);
            if (! $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 全体员工.
     */
    public function getAllStaff(int $id): array
    {
        return $this->model->query()->find($id)->users()->get(['id', 'username'])->toArray();
    }

    public function getAllDeptTree(int $id): array
    {
        $deptDate = $this->model::query()->find($id)->depts()->get()->toArray();
        return (new BaseCollection())->toTree($deptDate, $deptDate[0]['parent_id'] ?? 0);
    }

    /**
     * 查询部门名称.
     */
    public function getOrgName(?array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    /**
     * 判断是否存在子部门.
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 生成组织超级管理员.
     */
    #[ArrayShape(
        [
            'username' => 'string',
            'password' => 'string',
            'role_ids' => 'array',
            'org_id' => 'int',
        ]
    )]
    public function genSuperAdmin(int $id, array $data): void
    {
        $role_ids = $data['role_ids'] ?? [];
        $role_ids = array_merge($role_ids, [config('base-common.org_super_role_id')]);
        $username = 'org_admin_' . $id . '_' . Str::random(4);
        $orgSuperAdminInfo = [
            'username' => $username,
            'password' => config('base-common.default_password'),
            'role_ids' => $role_ids,
            'org_id' => $id,
        ];
        $userDao = di()->get(UserDao::class);
        $adminId = $userDao->save($orgSuperAdminInfo);
        $this->model::query()->find($id)->update(['super_admin_id' => $adminId]);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'status'),
            fn (Builder $query, $status) => $query->where('status', $status)
        );

        $query->when(
            Arr::get($params, 'level'),
            fn (Builder $query, $level) => $query->where('level', 'like', '%' . $level . '%')
        );

        $query->when(
            Arr::get($params, 'name'),
            fn (Builder $query, $name) => $query->where('name', 'like', '%' . $name . '%')
        );

        $query->when(
            Arr::get($params, 'province_region_id'),
            fn (Builder $query, $provinceRegionId) => $query->where('province_region_id', $provinceRegionId)
        );

        $query->when(
            Arr::get($params, 'city_region_id'),
            fn (Builder $query, $cityRegionId) => $query->where('city_region_id', $cityRegionId)
        );

        $query->when(
            Arr::get($params, 'address'),
            fn (Builder $query, $address) => $query->where('address', $address)
        );

        $query->when(
            Arr::get($params, 'legal_person'),
            fn (Builder $query, $legalPerson) => $query->where('legal_person', $legalPerson)
        );

        $query->when(
            Arr::get($params, 'phone'),
            fn (Builder $query, $phone) => $query->where('phone', $phone)
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
