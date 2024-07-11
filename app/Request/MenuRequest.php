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

class MenuRequest extends BaseFormRequest
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
            'ids.*' => ['required', 'integer', 'exists:menus,id'],
        ];
    }

    public function realDeleteRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:menus,id'],
        ];
    }

    public function recoveryRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:menus,id'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '菜单ID',
            'name' => '菜单名称',
            'code' => '菜单标识',
            'status' => '菜单状态',
            'menu_ids.*' => '菜单ID',
            'depts_ids.*' => '部门ID',
            'ids.*' => '菜单ID',
        ];
    }
}
