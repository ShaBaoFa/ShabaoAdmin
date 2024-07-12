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

namespace App\Request;

use App\Base\BaseFormRequest;

class RoleRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            'name' => 'required|max:30',
            'code' => 'required|min:3|max:100',
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['required_with:menu_ids', 'integer', 'exists:menus,id'],
            'depts_ids' => ['nullable', 'array'],
            'depts_ids.*' => ['required_with:depts_ids', 'integer', 'exists:departments,id'],
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            'name' => 'required|max:30',
            'code' => 'required|min:3|max:100',
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array.
     */
    public function changeStatusRules(): array
    {
        return [
            'id' => 'required',
            'status' => 'required',
        ];
    }

    public function deleteRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function realDeleteRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function recoveryRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '角色ID',
            'name' => '角色名称',
            'code' => '角色标识',
            'status' => '角色状态',
            'menu_ids.*' => '菜单ID',
            'depts_ids.*' => '部门ID',
            'ids.*' => '角色ID',
        ];
    }
}
