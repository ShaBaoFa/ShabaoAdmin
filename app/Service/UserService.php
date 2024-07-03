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
use App\Constants\ErrorCode;
use App\Dao\UserDao;
use App\Exception\BusinessException;
use App\Model\User;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;

class UserService extends BaseService
{
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 新增用户.
     */
    public function save(array $data): int
    {
        if ($this->dao->existsByUsername($data['username'])) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        return $this->dao->save($this->handleData($data));
    }

    /**
     * 获取用户信息.
     */
    public function info(?int $userId = null): array
    {
        if ($uid = (is_null($userId) ? user()->getId() : $userId)) {
            return $this->getCacheInfo($uid);
        }
        throw new BusinessException(ErrorCode::USER_NOT_EXIST);
    }

    /**
     * 更新用户信息.
     */
    #[CacheEvict(prefix: 'loginInfo', value: 'userId_#{id}')]
    public function update(mixed $id, array $data): bool
    {
        if (isset($data['username'])) {
            unset($data['username']);
        }
        if (isset($data['password'])) {
            unset($data['password']);
        }
        return $this->dao->update($id, $this->handleData($data));
    }

    /**
     * 获取缓存用户信息.
     */
    #[Cacheable(prefix: 'loginInfo', value: 'userId_#{id}', ttl: 0)]
    protected function getCacheInfo(int $id): array
    {
        $user = $this->dao->getModel()->find($id);
        $user->addHidden('deleted_at', 'password');
        $data['user'] = $user->toArray();

        /**
         * @todo 获取用户角色、部门、岗位信息
         */
        //        if (user()->isSuperAdmin()) {
        //            $data['roles'] = ['superAdmin'];
        //            $data['routers'] = $this->sysMenuService->mapper->getSuperAdminRouters();
        //            $data['codes'] = ['*'];
        //        } else {
        //            $roles = $this->sysRoleService->mapper->getMenuIdsByRoleIds($user->roles()->pluck('id')->toArray());
        //            $ids = $this->filterMenuIds($roles);
        //            $data['roles'] = $user->roles()->pluck('code')->toArray();
        //            $data['routers'] = $this->sysMenuService->mapper->getRoutersByIds($ids);
        //            $data['codes'] = $this->sysMenuService->mapper->getMenuCode($ids);
        //        }
        return $data;
    }

    /**
     * 处理提交数据.
     * @param mixed $data
     */
    protected function handleData(array $data): array
    {
        if (! is_array($data['role_ids'])) {
            $data['role_ids'] = explode(',', $data['role_ids']);
        }
        if (($key = array_search(env('ADMIN_ROLE'), $data['role_ids'])) !== false) {
            unset($data['role_ids'][$key]);
        }
        if (! empty($data['post_ids']) && ! is_array($data['post_ids'])) {
            $data['post_ids'] = explode(',', $data['post_ids']);
        }
        if (! empty($data['dept_ids']) && ! is_array($data['dept_ids'])) {
            $data['dept_ids'] = explode(',', $data['dept_ids']);
        }
        return $data;
    }
}
