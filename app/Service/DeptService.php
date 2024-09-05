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
use App\Dao\DeptDao;
use App\Exception\BusinessException;
use App\Model\Department;

class DeptService extends BaseService
{
    /**
     * @var DeptDao
     */
    public $dao;

    public function __construct(DeptDao $dao)
    {
        $this->dao = $dao;
    }

    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getTreeList($params, $isScope);
    }

    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getTreeListByRecycle($params, $isScope);
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        return $this->dao->getSelectTree();
    }

    /**
     * 新增部门.
     */
    public function save(array $data): mixed
    {
        return $this->dao->save($this->handleData($data));
    }

    /**
     * 更新部门.
     */
    public function update(mixed $id, array $data): bool
    {
        $handleData = $this->handleData($data);
        if (! $this->checkChildrenExists($id)) {
            return $this->dao->update($id, $handleData);
        }
        $update[] = [
            'id' => $id,
            'data' => $handleData,
        ];
        $descendants = $this->getDescendants(parentId: (int) $id);
        foreach ($descendants as $descendant) {
            $handleDescendantDeptLevelData = $this->handleDescendantLevels($descendant['level'], $handleData['level'], $id);
            $update[] = [
                'id' => $descendant['id'],
                'data' => ['level' => $handleDescendantDeptLevelData],
            ];
        }
        return $this->dao->batchUpdate($update);
    }

    /**
     * 真实删除部门.
     */
    public function realDel(array $ids): ?array
    {
        // 跳过的部门
        $ctuIds = [];
        if (count($ids)) {
            foreach ($ids as $id) {
                if (! $this->checkChildrenExists((int) $id) && ! $this->dao->find($id)->users()->exists()) {
                    $this->dao->realDelete([$id]);
                } else {
                    $ctuIds[] = $id;
                }
            }
        }
        return count($ctuIds) ? $this->dao->getDeptName($ctuIds) : null;
    }

    /**
     * 处理数据.
     */
    protected function handleData(array $data): array
    {
        $pid = (int) $data['parent_id'] ?? 0;
        if (isset($data['id']) && $data['id'] == $pid) {
            throw new BusinessException(ErrorCode::DEPT_PARENT_NOT_VALID);
        }
        if ($pid === 0) {
            $data['level'] = $data['parent_id'] = '0';
        } else {
            $parent = $this->find($data['parent_id']);
            /**
             * @var Department $parent
             */
            $data['level'] = $parent->level . ',' . $data['parent_id'];
        }

        return $data;
    }
}
