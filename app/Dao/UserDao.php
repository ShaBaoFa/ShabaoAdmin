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

    public function save(array $data): User
    {
        $model = new User();
        $model->username = $data['username'];
        $model->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $model->save();
        return $model;
    }

    public function findByUsername(array $array, bool $throw = false): ?User
    {
        $model = User::query()->where('username', $array['username'])->first();
        if (! $model && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }
}
