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

use App\Base\BaseCollection;
use App\Base\BaseDao;
use App\Model\Department;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\DbConnection\Db;

use function App\Helper\filled;
use function App\Helper\user;
use function Hyperf\Config\config;

class DeptDao extends BaseDao
{
    /**
     * @var Department
     */
    public $model;

    /**
     * 查询的菜单字段.
     */
    public array $deptField = ['id', 'parent_id', 'level', 'name', 'leader', 'phone', 'status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    public function assignModel(): void
    {
        $this->model = Department::class;
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        $treeData = $this->model::query()->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->where('status', $this->model::ENABLE)
            ->orderBy('parent_id')
            ->orderBy('sort', 'desc')
            ->userDataScope()
            ->get()->toArray();

        $deptTree = (new BaseCollection())->toTree($treeData, $treeData[0]['parent_id'] ?? 0);

        if (config('base-common.data_scope_enabled', true) && ! user()->isSuperAdmin()) {
            $deptIds = Db::table(table: 'department_user')->where('user_id', '=', user()->getId())->pluck('department_id');
            $treeData = $this->model::query()
                ->select(['id', 'parent_id', 'id AS value', 'name AS label'])
                ->whereIn('id', $deptIds)
                ->where('status', $this->model::ENABLE)
                ->orderBy('parent_id')->orderBy('sort', 'desc')
                ->get()->toArray();

            // 去除重复部门
            $deptTree = array_merge($treeData, $deptTree);
            $deptTree = array_values(array_column($deptTree, null, 'id'));

            return (new BaseCollection())->toTree($deptTree, $treeData[0]['parent_id'] ?? 0);
        }
        return $deptTree;
    }

    /**
     * 批量更新.
     */
    #[Transactional]
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
     * 查询部门名称.
     */
    public function getDeptName(?array $ids = null): array
    {
        return $this->model::withTrashed()->whereIn('id', $ids)->pluck('name')->toArray();
    }

    /**
     * 判断是否存在子部门.
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->model::withTrashed()->where('parent_id', $id)->exists();
    }

    /**
     * 获取子孙部门.
     */
    public function getDescendantsDepts(int $id): array
    {
        $params = ['level' => $id];
        return $this->handleSearch($this->model::query(), $params)->get()->toArray();
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['level']) && filled($params['level'])) {
            $query->where('level', 'like', '%' . $params['level'] . '%');
        }

        if (isset($params['name']) && filled($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['leader']) && filled($params['leader'])) {
            $query->where('leader', $params['leader']);
        }

        if (isset($params['phone']) && filled($params['phone'])) {
            $query->where('phone', $params['phone']);
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }
        return $query;
    }
}
