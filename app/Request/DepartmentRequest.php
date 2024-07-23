<?php

declare(strict_types=1);

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
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array.
     */
    public function changeStatusRules(): array
    {
        return [
            'id' => ['required','integer','exists:departments,id'],
            'status' => ['required','integer','in:1,2'],
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
            'id' => '部门ID',
            'ids.*' => '部门ID',
            'name' => '部门名称',
            'status' => '部门状态',
            'leader' => '部门负责人',
            'phone' => '部门电话',
        ];
    }
}
