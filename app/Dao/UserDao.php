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
use App\Model\User;
use Hyperf\DbConnection\Annotation\Transactional;

class UserDao extends BaseDao
{
    public $model;

    public function assignModel(): void
    {
        $this->model = User::class;
    }

    public function first(int $id, bool $throw = false): ?User
    {
        $model = User::findFromCache($id);
        if (! $model && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    #[Transactional]
    public function save(array $data): int
    {
        $this->filterExecuteAttributes($data, true);
        $user = $this->model::create($data);
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
        $role_ids = $data['role_ids'] ?? [];
        $post_ids = $data['post_ids'] ?? [];
        $dept_ids = $data['dept_ids'] ?? [];
        $this->filterExecuteAttributes($data, true);

        $result = parent::update($id, $data);
        $user = $this->model::find($id);
        if ($user && $result) {
            ! empty($role_ids) && $user->roles()->sync($role_ids);
            ! empty($dept_ids) && $user->depts()->sync($dept_ids);
            $user->posts()->sync($post_ids);
            return true;
        }
        return false;
    }
}
