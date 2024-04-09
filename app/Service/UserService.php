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
use App\Service\Dao\UserDao;
use Hyperf\Di\Annotation\Inject;

class UserService extends BaseService
{
    #[Inject]
    protected UserDao $userDao;

    public function store(array $data): User
    {
        return $this->userDao->save($data);
    }

    public function login(array $data): User
    {
        return $this->userDao->findByAccount(['account' => $data['account'], 'password' => $data['password']]);
    }
}
