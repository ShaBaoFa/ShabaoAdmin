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

class OrganizationRequest extends BaseFormRequest
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
        // 'name', 'address', 'legal_person', 'phone'
        return [
            'name' => 'required|max:30',
            'address' => 'nullable|string|max:255',
            'legal_person' => 'nullable|string|max:30',
            'phone' => ['nullable', 'string', 'telephone_number'],
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'required_with:role_ids|integer|exists:roles,id',
            'parent_id' => 'nullable|integer|exists:organizations,id',
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
            'address' => 'nullable|string|max:255',
            'legal_person' => 'nullable|string|max:30',
            'phone' => ['nullable', 'string', 'telephone_number'],
            'parent_id' => 'nullable|integer|exists:organizations,id',
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array.
     */
    public function changeStatusRules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:organizations,id'],
            'status' => ['required', 'integer', 'in:1,2'],
        ];
    }

    public function deleteRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:organizations,id'],
        ];
    }

    public function realDeleteRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:organizations,id'],
        ];
    }

    public function recoveryRules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:organizations,id'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '组织ID',
            'ids.*' => '组织ID',
            'name' => '组织名称',
            'status' => '组织状态',
            'leader' => '组织负责人',
            'phone' => '组织电话',
            'parent_id' => '上级组织',
        ];
    }
}
