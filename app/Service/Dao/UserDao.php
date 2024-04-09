<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service\Dao;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Model\User;

class UserDao extends BaseDao
{
    public function first(int $id, bool $throw = false): ?User
    {
        $model = User::findFromCache($id);
        if (! $model && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $model;
    }

    public function save(array $input): User
    {
        $model = new User();
        $model->account = $input['account'];
        $model->password = hash('sha256', $input['password']);
        $model->save();
        return $model;
    }

    public function findByAccount(array $array, bool $throw = false): ?User
    {
        $model = User::query()->where('account', $array['account'])->first();
        if (! $model && $throw) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        if (hash_equals($model?->password, hash('sha256', $array['password'])) === false) {
            throw new BusinessException(ErrorCode::USER_PASSWORD_ERROR);
        }
        var_dump($model->toArray());
        return $model;
    }
}
