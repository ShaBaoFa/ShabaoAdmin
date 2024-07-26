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
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\Stringable\Str;
use JetBrains\PhpStorm\ArrayShape;

use function App\Helper\filled;
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
     * 获取子孙部门.
     */
    public function getDescendantsOrgs(int $id): array
    {
        $params = ['level' => $id];
        return $this->handleSearch($this->model::query(), $params)->get()->toArray();
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
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['level']) && filled($params['level'])) {
            $query->where('level', 'like', '%' . $params['level'] . '%');
        }

        if (isset($params['name']) && filled($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['address']) && filled($params['address'])) {
            $query->where('address', $params['address']);
        }

        if (isset($params['legal_person']) && filled($params['legal_person'])) {
            $query->where('legal_person', $params['legal_person']);
        }
        if (isset($params['phone']) && filled($params['phone'])) {
            $query->where('phone', $params['phone']);
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
