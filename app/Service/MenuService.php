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
use App\Exception\BusinessException;
use App\Model\Menu;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MenuService extends BaseService
{
    /**
     * @var MenuDao
     */
    public $dao;

    public function __construct(MenuDao $dao)
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
    public function getSelectTree(array $data): array
    {
        return $this->dao->getSelectTree($data);
    }

    /**
     * 通过code获取菜单名称.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findNameByCode(string $code): string
    {
        if (strlen($code) < 1) {
            throw new BusinessException(ErrorCode::MENU_CODE_NOT_EXIST);
        }
        $name = $this->dao->findNameByCode($code);
        return $name ?? throw new BusinessException(ErrorCode::MENU_CODE_NOT_EXIST);
    }

    /**
     * 新增菜单.
     */
    public function save(array $data): mixed
    {
        $id = $this->dao->save($this->handleData($data));

        // 生成RESTFUL按钮菜单
        if ($data['type'] == Menu::MENUS_LIST && $data['gen_btn'] === Menu::GEN_BTN) {
            $model = $this->dao->model::find($id, ['id', 'name', 'code']);
            $this->genButtonMenu($model);
        }

        return $id;
    }

    /**
     * 生成按钮菜单.
     */
    public function genButtonMenu(Menu $model): bool
    {
        $buttonMenus = [
            ['name' => $model->name . '列表', 'code' => $model->code . ':index'],
            ['name' => $model->name . '回收站', 'code' => $model->code . ':recycle'],
            ['name' => $model->name . '保存', 'code' => $model->code . ':save'],
            ['name' => $model->name . '更新', 'code' => $model->code . ':update'],
            ['name' => $model->name . '删除', 'code' => $model->code . ':delete'],
            ['name' => $model->name . '信息', 'code' => $model->code . ':info'],
            ['name' => $model->name . '恢复', 'code' => $model->code . ':recovery'],
            ['name' => $model->name . '真实删除', 'code' => $model->code . ':realDelete'],
            ['name' => $model->name . '导入', 'code' => $model->code . ':import'],
            ['name' => $model->name . '导出', 'code' => $model->code . ':export'],
        ];

        foreach ($buttonMenus as $button) {
            $this->save(
                array_merge(
                    ['parent_id' => $model->id, 'type' => Menu::BUTTON],
                    $button
                )
            );
        }

        return true;
    }

    /**
     * 更新菜单.
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
            $handleDescendantMenuLevelData = $this->handleDescendantLevels($descendant['level'], $handleData['level'], $id);
            $update[] = [
                'id' => $descendant['id'],
                'data' => ['level' => $handleDescendantMenuLevelData],
            ];
        }
        return $this->dao->batchUpdate($update);
    }

    /**
     * 真实删除菜单.
     */
    public function realDel(array $ids): ?array
    {
        // 跳过的菜单
        $ctuIds = [];
        if (count($ids)) {
            foreach ($ids as $id) {
                if (! $this->checkChildrenExists((int) $id)) {
                    $this->dao->realDelete([$id]);
                } else {
                    $ctuIds[] = $id;
                }
            }
        }
        return count($ctuIds) ? $this->dao->getMenuName($ctuIds) : null;
    }

    /**
     * 处理数据.
     */
    protected function handleData(array $data): array
    {
        if (empty($data['parent_id']) || $data['parent_id'] == 0) {
            $data['level'] = '0';
            $data['parent_id'] = 0;
            $data['type'] = $data['type'] === Menu::BUTTON ? Menu::MENUS_LIST : $data['type'];
        } else {
            $parentMenu = $this->dao->find((int) $data['parent_id']);
            $data['level'] = $parentMenu['level'] . ',' . $parentMenu['id'];
        }
        return $data;
    }
}
