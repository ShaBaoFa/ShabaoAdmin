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

namespace App\Service;

use App\Dao\UserDao;
use App\Model\User;
use App\Resource\UserResource;

class UserService extends BaseService
{
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    public function save(array $data): UserResource
    {
        $model = $this->dao->save($data);
        return new UserResource($model);
    }

    public function login(array $data): User
    {
        return $this->dao->findByUsername(['username' => $data['username'], 'password' => $data['password']]);
    }

    public function info(int $checkAndGetId): UserResource
    {
        $model = $this->dao->first($checkAndGetId, true);

        return new UserResource($model);
    }
}
