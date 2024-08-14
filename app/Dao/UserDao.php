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
use App\Base\BaseModel;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\Department;
use App\Model\User;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;

use function App\Helper\user;
use function Hyperf\Support\env;

class UserDao extends BaseDao
{
    /**
     * @var User
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = User::class;
    }

    /**
     * 检查用户密码
     */
    public function checkPass(string $password, string $hash): bool
    {
        return $this->model::passwordVerify($password, $hash);
    }

    /**
     * 判断是否归属该组织.
     */
    public function belongThisOrg(int $id): bool
    {
        if (user()->isSuperAdmin()) {
            return true;
        }
        return $this->model->find(user()->getId())->organizations()->first()->value($this->model->getKeyName()) == $id;
    }

    /**
     * 初始化用户密码
     */
    public function initUserPassword(int $id, string $password): bool
    {
        $model = $this->model::find($id);
        if ($model) {
            $model->password = $password;
            return $model->save();
        }
        return false;
    }

    #[Transactional]
    public function save(array $data): int
    {
        $role_ids = $data['role_ids'] ?? [];
        //        $post_ids = $data['post_ids'] ?? [];
        $dept_ids = $data['dept_ids'] ?? [];
        $org_id = $data['org_id'] ?? 0;
        $this->filterExecuteAttributes($data, true);
        $user = $this->model::create($data);
        $user->roles()->sync($role_ids, false);
        $user->organizations()->sync($org_id, false);
        //        $user->posts()->sync($post_ids, false);
        $user->depts()->sync($dept_ids, false);
        return $user->getKey();
    }

    public function findByUsername(string $username, bool $throw = false): ?User
    {
        $model = $this->model::query()->where('username', $username)->first();
        if (! $model && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    /**
     * 通过用户名检查是否存在.
     */
    public function existsByUsername(string $username): bool
    {
        return $this->model::query()->where('username', $username)->exists();
    }

    /**
     * 更新用户.
     */
    #[Transactional]
    public function update(mixed $id, array $data): bool
    {
        //        $post_ids = $data['post_ids'] ?? [];
        $role_ids = $data['role_ids'] ?? [];
        $dept_ids = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);

        $result = parent::update($id, $data);
        $user = $this->model::find($id);
        if ($user && $result) {
            ! empty($role_ids) && $user->roles()->sync($role_ids);
            ! empty($dept_ids) && $user->depts()->sync($dept_ids);
            //            $user->posts()->sync($post_ids);
            return true;
        }
        return false;
    }

    /**
     * 真实批量删除用户.
     */
    #[Transactional]
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $user = $this->model::withTrashed()->find($id);
            if ($user) {
                $user->roles()->detach();
                $user->organizations()->detach();
                //                $user->posts()->detach();
                $user->depts()->detach();
                $user->forceDelete();
            }
        }
        return true;
    }

    /**
     * 获取用户信息.
     */
    public function find(mixed $id, array $column = ['*']): ?BaseModel
    {
        $user = $this->model::find($id);
        if ($user) {
            $user->setAttribute('roleList', $user->roles()->get(['id', 'name']) ?: []);
            $user->setAttribute('organization', $user->organizations()->first(['id', 'name']) ?: null);
            //            $user->setAttribute('postList', $user->posts()->get(['id', 'name']) ?: []);
            $user->setAttribute('deptList', $user->depts()->get(['id', 'name']) ?: []);
        }
        return $user;
    }

    /**
     * 根据用户ID列表获取用户基础信息.
     */
    public function getUserInfoByIds(array $ids, ?array $select = ['id', 'username', 'phone', 'created_at']): array
    {
        return $this->model::query()->whereIn('id', $ids)->select($select)->get()->toArray();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'dept_id'),
            function (Builder $query, $deptId) {
                $deptIds = Department::query()
                    ->where(function ($query) use ($deptId) {
                        $query->where('id', '=', $deptId)
                            ->orWhere('level', 'like', $deptId . ',%')
                            ->orWhere('level', 'like', '%,' . $deptId)
                            ->orWhere('level', 'like', '%,' . $deptId . ',%');
                    })
                    ->pluck('id')
                    ->toArray();
                $query->whereHas('depts', fn ($query) => $query->whereIn('id', $deptIds));
            }
        );

        $query->when(
            trim(Arr::get($params, 'username')),
            fn (Builder $query, $username) => $query->where('username', 'like', '%' . $username . '%')
        );

        $query->when(
            Arr::get($params, 'phone'),
            fn (Builder $query, $phone) => $query->where('phone', '=', $phone)
        );

        $query->when(
            Arr::get($params, 'status'),
            fn (Builder $query, $status) => $query->where('status', $status)
        );

        $query->when(
            Arr::get($params, 'filterSuperAdmin'),
            fn (Builder $query) => $query->whereNotIn('id', [env('SUPER_ADMIN')])
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

        $query->when(
            Arr::get($params, 'userIds'),
            fn (Builder $query, $userIds) => $query->whereIn('id', $userIds)
        );

        $query->when(
            Arr::get($params, 'showDept'),
            function (Builder $query) use ($params) {
                $isAll = Arr::get($params, 'showDeptAll', false);
                $query->with(['depts' => function ($query) use ($isAll) {
                    $query->where('status', Department::ENABLE);
                    return $isAll ? $query->select(['*']) : $query->select(['id', 'name']);
                }]);
            }
        );

        $query->when(
            Arr::get($params, 'role_ids'),
            fn (Builder $query, $roleIds) => $query->whereHas('roles', fn ($query) => $query->whereIn('roles.id', $roleIds))
        );

        $query->when(
            Arr::get($params, 'org_id'),
            fn (Builder $query, $orgId) => $query->whereHas('organizations', fn ($query) => $query->whereIn('organizations.id', $orgId))
        );

        return $query;
    }
}
