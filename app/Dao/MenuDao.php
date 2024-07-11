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
use App\Model\Menu;
use App\Model\User;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Annotation\Transactional;
use RedisException;
use function Hyperf\Config\config;

class MenuDao extends BaseDao
{
    /**
     * @var Menu
     */
    public $model;

    /**
     * 查询的菜单字段.
     */
    public array $menuField = [
        'id',
        'parent_id',
        'name',
        'code',
        'icon',
        'route',
        'is_hidden',
        'component',
        'redirect',
        'type',
    ];

    public function assignModel(): void
    {
        $this->model = Menu::class;
    }

    /**
     * 获取超级管理员（创始人）的路由菜单.
     */
    public function getSuperAdminRouters(): array
    {
        return $this->model::query()
            ->select($this->menuField)
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->get()->menuToRouterTree();
    }

    /**
     * 通过菜单ID列表获取菜单数据.
     */
    public function getRoutersByIds(array $ids): array
    {
        return $this->model::query()
            ->select($this->menuField)
            ->whereIn('id', $ids)
            ->where('status', $this->model::ENABLE)
            ->orderBy('sort', 'desc')
            ->get()->menuToRouterTree();
    }

    /**
     * 获取前端选择树.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getSelectTree(array $data): array
    {
        $query = $this->model::query()->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->where('status', $this->model::ENABLE)->orderBy('sort', 'desc');

        if (($data['scope'] ?? false) && ! user()->isSuperAdmin()) {
            $roleData = di()->get(RoleDao::class)->getMenuIdsByRoleIds(
                User::find(user()->getId(), ['id'])->roles()->pluck('id')->toArray()
            );

            $ids = [];
            foreach ($roleData as $val) {
                foreach ($val['menus'] as $menu) {
                    $ids[] = $menu['id'];
                }
            }
            unset($roleData);
            $query->whereIn('id', array_unique($ids));
        }

        if (! empty($data['onlyMenu'])) {
            $query->where('type', $this->model::MENUS_LIST);
        }

        return $query->get()->toTree();
    }

    /**
     * 获取子孙menus.
     */
    public function getDescendantsMenus(int $parentId): array
    {
        $params = ['level' => $parentId];
        return $this->handleSearch($this->model::query(), $params)->get()->toArray();
    }

    /**
     * 查询菜单code.
     */
    public function getMenuCode(?array $ids = null): array
    {
        return $this->model::query()->whereIn('id', $ids)->pluck('code')->toArray();
    }

    /**
     * 通过 code 查询菜单名称.
     */
    public function findNameByCode(string $code): ?string
    {
        return $this->model::query()->where('code', $code)->value('name');
    }

    /**
     * 单个或批量真实删除数据.
     */
    #[CacheEvict(prefix: 'loginInfo', all: true), Transactional]
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                $model->roles()->detach();
                $model->forceDelete();
            }
        }
        return true;
    }

    /**
     * 新增菜单.
     */
    #[CacheEvict(prefix: 'loginInfo', all: true)]
    public function save(array $data): mixed
    {
        return parent::save($data);
    }

    /**
     * 更新菜单.
     */
    #[CacheEvict(prefix: 'loginInfo', all: true)]
    public function update(mixed $id, array $data): bool
    {
        return parent::update($id, $data);
    }

    /**
     * 批量更新菜单
     * @param array $update
     * @return bool
     */
    #[CacheEvict(prefix: 'loginInfo', all: true),Transactional]
    public function batchUpdate(array $update): bool
    {
        foreach ($update as $item) {
            $result = parent::update($item['id'], $item['data']);
            if (! $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 逻辑删除菜单.
     */
    #[CacheEvict(prefix: 'loginInfo', all: true)]
    public function delete(array $ids): bool
    {
        return parent::delete($ids);
    }

    /**
     * 通过 route 查询菜单.
     */
    public function findMenuByRoute(string $route)
    {
        return $this->model::query()->where('route', $route)->first();
    }

    /**
     * 查询菜单code.
     */
    public function getMenuName(?array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }



        if (isset($params['name']) && filled($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }

        if (isset($params['noButton']) && filled($params['noButton']) && $params['noButton'] === true) {
            $query->where('type', '<>', $this->model::BUTTON);
        }
        return $query;
    }
}
