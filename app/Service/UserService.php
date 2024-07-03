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
use Hyperf\Redis\Redis;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Xmo\JWTAuth\JWT;
use function Hyperf\Config\config;

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
     * 获取在线用户.
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    public function getOnlineUserPageList(array $params = []): array
    {
        $redis = di()->get(Redis::class);
        $key = sprintf('%sToken:*', config('cache.default.prefix'));
        $userIds = [];
        $iterator = null;

        while (false !== ($users = $redis->scan($iterator, $key, 100))) {
            foreach ($users as $user) {
                var_dump('user:'.$user);
                var_dump('key:'.$key);
                // 如果是已经加入到黑名单的就代表不是登录状态了
                if (! $this->hasTokenBlack($redis->get($user)) && preg_match("/{$key}(\\d+)$/", $user, $match) && isset($match[1])) {
                    $userIds[] = $match[1];
                }
            }
            var_dump($userIds);
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
     * @throws \RedisException
     */
    public function kickUser(string $id): bool
    {
        $redis = di()->get(Redis::class);
        $key = sprintf('%sToken:%s', config('cache.default.prefix'), $id);
        $token = $redis->get($key);
        if (! is_string($token)) {
            throw new BusinessException(ErrorCode::USER_NOT_EXIST);
        }
        user()->getJwt()->logout($redis->get($key), 'default');
        $redis->del($key);
        return true;
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    private function hasTokenBlack(string $token): bool
    {
        $jwt = di()->get(JWT::class);
        $scenes = array_keys(config('jwt.scene'));
        foreach ($scenes as $scene) {
            $sceneJwt = $jwt->setScene($scene);
            if ($sceneJwt->blackList->hasTokenBlack(
                $sceneJwt->getParserData($token),
                $jwt->getSceneConfig($scene)
            )) {
                return true;
            }
        }
        return false;
    }
}
