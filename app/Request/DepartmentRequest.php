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

class DepartmentRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            'name' => 'required|max:30',
            'leader' => 'nullable|string|max:30',
            'phone' => ['nullable', 'string', 'telephone_number'],
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
            'leader' => 'nullable|string|max:30',
            'phone' => ['nullable', 'string', 'telephone_number'],
            'parent_id' => 'nullable|integer|exists:departments,id',
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array.
     */
    public function changeStatusRules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:departments,id'],
            'status' => ['required', 'integer', 'in:1,2'],
        ];
    }

    public function deleteRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function realDeleteRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function recoveryRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '部门ID',
            'ids.*' => '部门ID',
            'name' => '部门名称',
            'status' => '部门状态',
            'leader' => '部门负责人',
            'phone' => '部门电话',
            'parent_id' => '上级部门',
        ];
    }
}
