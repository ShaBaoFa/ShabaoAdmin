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

namespace App\Service;

use App\Base\BaseService;
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
