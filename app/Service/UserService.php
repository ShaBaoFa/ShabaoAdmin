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
use App\Dao\MenuDao;
use App\Dao\RoleDao;
use App\Dao\UserDao;
use App\Events\AfterKickUser;
use App\Exception\BusinessException;
use App\Model\User;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Collection\Arr;
use Hyperf\Event\EventDispatcher;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RedisException;
use Xmo\JWTAuth\JWT;

use function App\Helper\user;
use function Hyperf\Config\config;
use function Hyperf\Support\env;

class UserService extends BaseService
{
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    public function myCollectObjs(array $params): array
    {
        $ids = $this->dao->getMyCollectObjsIds();
        if(count($ids) == 0) return [];
        $eloS = di()->get(ExhLibObjService::class);
        Arr::set($params, 'ids', $ids);
        return $eloS->getPublicIndex($params);
    }

    public function myPickObjs(array $params): array
    {
        $ids = $this->dao->getMyPickObjsIds();
        if(count($ids) == 0) return [];
        $eloS = di()->get(ExhLibObjService::class);
        Arr::set($params, 'ids', $ids);
        return $eloS->getPublicIndex($params);
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
    #[CacheEvict(prefix: 'LoginInfo', value: 'UserId_#{id}')]
    public function update(mixed $id, array $data): bool
    {
        if (Arr::has($data, 'username')) {
            Arr::forget($data, 'username');
        }
        if (Arr::has($data, 'password')) {
            Arr::forget($data, 'password');
        }
        return $this->dao->update($id, $this->handleData($data));
    }

    /**
     * 获取在线用户.
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getOnlineUserPageList(array $params = []): array
    {
        $redis = di()->get(Redis::class);
        $key = sprintf('%sToken:*', config('cache.default.prefix'));
        $userIds = [];
        $iterator = null;
        while (false !== ($users = $redis->scan($iterator, $key, 100))) {
            foreach ($users as $user) {
                // 如果是已经加入到黑名单的就代表不是登录状态了
                // 重写正则 用来 匹配 多点登录 使用的token的key
                if (! $this->hasTokenBlack($redis->get($user)) && preg_match('/:(\d+)(:|$)/', $user, $match) && Arr::has($match, 1)) {
                    $userIds[] = $match[1];
                }
            }
            unset($users);
        }

        if (empty($userIds)) {
            return [];
        }
        return $this->getPageList(array_merge(['userIds' => $userIds], $params));
    }

    /**
     * 强制下线用户.
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function kickUser(string $id): bool
    {
        $redis = di()->get(Redis::class);
        // 保证获取到所有token，方便一次性全部下线。
        $iterator = null;
        $key = sprintf('%sToken:%s*', config('cache.default.prefix'), $id);
        while (false !== ($users = $redis->scan($iterator, $key, 100))) {
            $jwt = di()->get(JWT::class);
            foreach ($users as $user) {
                $token = $redis->get($user);
                if (! is_string($token)) {
                    continue;
                }
                $scene = $jwt->getParserData($token)['jwt_scene'];
                $jwt->logout($token, $scene);
                $redis->del($user);
            }
            unset($users);
        }
        $evDispatcher = di()->get(EventDispatcher::class);
        $evDispatcher->dispatch(new AfterKickUser(['uid' => $id]));
        return true;
    }

    /**
     * 删除用户.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete(array $ids): bool
    {
        if (! empty($ids)) {
            if (($key = array_search(env('SUPER_ADMIN'), $ids)) !== false) {
                unset($ids[$key]);
            }
            return $this->dao->delete($ids);
        }

        return false;
    }

    /**
     * 真实删除用户.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function realDelete(array $ids): bool
    {
        if (! empty($ids)) {
            if (($key = array_search(env('SUPER_ADMIN'), $ids)) !== false) {
                unset($ids[$key]);
            }
            return $this->dao->realDelete($ids);
        }

        return false;
    }

    /**
     * 初始化用户密码
     */
    public function initUserPassword(int $id, string $password = '123456'): bool
    {
        return $this->dao->initUserPassword($id, $password);
    }

    /**
     * 用户修改密码
     */
    public function modifyPassword(array $params): bool
    {
        return $this->dao->initUserPassword(user()->getId(), $params['newPassword']);
    }

    /**
     * 用户更新个人资料.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[CacheEvict(prefix: 'LoginInfo', value: 'UserId_#{id}')]
    public function updateInfo(int $id, array $params): bool
    {
        if (! $id) {
            return false;
        }
        # 用户更新个人资料
        unset($params['id'], $params['username'], $params['password'], $params['status'], $params['user_type']);
        return $this->dao->update($id, $params);
    }

    /**
     * 获取缓存用户信息.
     */
    #[Cacheable(prefix: 'LoginInfo', value: 'UserId_#{id}', ttl: 0)]
    protected function getCacheInfo(int $id): array
    {
        /** @var User $user */
        $user = $this->dao->getModel()->find($id);
        $user->addHidden('deleted_at', 'password');
        $data['user'] = $user->toArray();

        /**
         * @todo 获取用户角色、部门、岗位信息
         */
        /**
         * @var MenuDao $menuDao
         */
        $menuDao = di()->get(MenuDao::class);
        if (user()->isSuperAdmin()) {
            $data['roles'] = ['superAdmin'];
            $data['routers'] = $menuDao->getSuperAdminRouters();
            $data['codes'] = ['*'];
        } else {
            $roleDao = di()->get(RoleDao::class);
            $roles = $roleDao->getMenuIdsByRoleIds($user->roles()->pluck('id')->toArray());
            $ids = $this->filterMenuIds($roles);
            $data['organization'] = $user->organizations()->first(['id', 'name']);
            $data['roles'] = $user->roles()->get(['name', 'code'])->toArray();
            $data['routers'] = $menuDao->getRoutersByIds($ids);
            $data['codes'] = $menuDao->getMenuCode($ids);
        }
        return $data;
    }

    /**
     * 过滤通过角色查询出来的菜单id列表，并去重.
     */
    protected function filterMenuIds(array &$roleData): array
    {
        $ids = [];
        foreach ($roleData as $val) {
            foreach ($val['menus'] as $menu) {
                $ids[] = $menu['id'];
            }
        }
        unset($roleData);
        return array_unique($ids);
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    private function hasTokenBlack(string $token): bool
    {
        # token解析的数据有scene信息，只需要判断当前token在对应场景下是否有黑名单
        $jwt = di()->get(JWT::class);
        $scene = $jwt->getParserData($token)['jwt_scene'];
        $scenes = array_keys(config('jwt.scene'));
        $jti = $jwt->getParserData($token)['jti'];
        if (in_array($scene, $scenes) && $jwt->setScene($scene)->blackList->hasTokenBlack(
            $jwt->getParserData($token),
            $jwt->getSceneConfig($scene)
        )) {
            return true;
        }
        return false;
    }
}
