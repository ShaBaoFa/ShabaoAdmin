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
use App\Dao\RoleDao;
use App\Exception\BusinessException;

class RoleService extends BaseService
{
    public function __construct(RoleDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取角色列表，并过滤掉超管角色.
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        $params['filterAdminRole'] = true;
        return parent::getList($params, $isScope);
    }

    public function save(array $data): mixed
    {
        if ($this->dao->checkRoleCode($data['code'])) {
            throw new BusinessException(ErrorCode::ROLE_CODE_NOT_EXIST);
        }
        return $this->dao->save($data);
    }

    /**
     * 通过角色获取菜单.
     */
    public function getMenuByRole(int $id): array
    {
        return $this->dao->getMenuIdsByRoleIds(['ids' => $id]);
    }

    /**
     * 通过角色获取部门.
     */
    public function getDeptByRole(int $id): array
    {
        return $this->dao->getDeptIdsByRoleIds(['ids' => $id]);
    }

    /**
     * 通过code获取角色名称.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function findNameByCode(string $code): string
    {
        if (strlen($code) < 1) {
            throw new BusinessException(ErrorCode::ROLE_CODE_NOT_EXIST);
        }
        $name = $this->dao->findNameByCode($code);
        return $name ?? throw new BusinessException(ErrorCode::ROLE_CODE_NOT_EXIST);
    }

    /**
     * 更新角色信息.
     */
    public function update(mixed $id, array $data): bool
    {
        return $this->dao->update($id, $data);
    }
}
