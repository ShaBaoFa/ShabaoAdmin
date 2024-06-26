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

use App\Model\User;
use App\Resource\UserResource;
use App\Service\Dao\UserDao;
use Hyperf\Di\Annotation\Inject;

class UserService extends BaseService
{
    #[Inject]
    protected UserDao $userDao;

    public function store(array $data): UserResource
    {
        $model = $this->userDao->save($data);
        return new UserResource($model);
    }

    public function login(array $data): User
    {
        return $this->userDao->findByUsername(['username' => $data['username'], 'password' => $data['password']]);
    }

    public function info(int $checkAndGetId): UserResource
    {
        $model = $this->userDao->first($checkAndGetId, true);

        return new UserResource($model);
    }
}
