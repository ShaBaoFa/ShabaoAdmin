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

namespace App\Base\Trait;

use App\Base\BaseDao;
use App\Base\BaseModel;
use Hyperf\DbConnection\Db;
use Hyperf\Tappable\HigherOrderTapProxy;

trait ServiceTrait
{
    /**
     * @var BaseDao
     */
    public $dao;

    /**
     * 获取列表数据.
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->dao->getList($params, $isScope);
    }

    /**
     * 从回收站过去列表数据.
     */
    public function getListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->dao->getList($params, $isScope);
    }

    /**
     * 获取列表数据（带分页）.
     */
    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        return $this->dao->getPageList($params, $isScope);
    }

    /**
     * 从回收站获取列表数据（带分页）.
     */
    public function getPageListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->dao->getPageList($params, $isScope);
    }

    /**
     * 获取树列表.
     */
    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->dao->getTreeList($params, $isScope);
    }

    /**
     * 从回收站获取树列表.
     */
    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->dao->getTreeList($params, $isScope);
    }

    /**
     * 新增数据.
     */
    public function save(array $data): mixed
    {
        return $this->dao->save($data);
    }

    /**
     * 批量新增.
     */
    public function batchSave(array $collects): bool
    {
        return Db::transaction(function () use ($collects) {
            foreach ($collects as $collect) {
                $this->dao->save($collect);
            }
            return true;
        });
    }

    /**
     * 读取一条数据.
     */
    public function find(mixed $id, array $column = ['*']): ?BaseModel
    {
        return $this->dao->find($id, $column);
    }

    /**
     * Description:获取单个值
     * @return null|HigherOrderTapProxy|mixed|void
     */
    public function value(array $condition, string $columns = 'id')
    {
        return $this->dao->value($condition, $columns);
    }

    /**
     * Description:获取单列值
     */
    public function pluck(array $condition, string $columns = 'id'): array
    {
        return $this->dao->pluck($condition, $columns);
    }

    /**
     * 从回收站读取一条数据.
     */
    public function findByRecycle(mixed $id): BaseModel
    {
        return $this->dao->findByRecycle($id);
    }

    /**
     * 单个或批量软删除数据.
     */
    public function delete(array $ids): bool
    {
        return ! empty($ids) && $this->dao->delete($ids);
    }

    /**
     * 更新一条数据.
     */
    public function update(mixed $id, array $data): bool
    {
        return $this->dao->update($id, $data);
    }

    /**
     * 按条件更新数据.
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        return $this->dao->updateByCondition($condition, $data);
    }

    /**
     * 单个或批量真实删除数据.
     */
    public function realDelete(array $ids): bool
    {
        return ! empty($ids) && $this->dao->realDelete($ids);
    }

    /**
     * 单个或批量从回收站恢复数据.
     */
    public function recovery(array $ids): bool
    {
        return ! empty($ids) && $this->dao->recovery($ids);
    }

    /**
     * 单个或批量禁用数据.
     */
    public function disable(array $ids, string $field = 'status'): bool
    {
        return ! empty($ids) && $this->dao->disable($ids, $field);
    }

    /**
     * 单个或批量启用数据.
     */
    public function enable(array $ids, string $field = 'status'): bool
    {
        return ! empty($ids) && $this->dao->enable($ids, $field);
    }

    /**
     * 修改数据状态
     */
    public function changeStatus(mixed $id, string $value, string $filed = 'status'): bool
    {
        return $value == BaseModel::ENABLE ? $this->dao->enable([$id], $filed) : $this->dao->disable([$id], $filed);
    }

    /**
     * 数字更新操作.
     */
    public function numberOperation(mixed $id, string $field, int $value): bool
    {
        return $this->dao->numberOperation($id, $field, $value);
    }
}
