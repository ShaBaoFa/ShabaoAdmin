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
use App\Constants\ErrorCode;
use App\Model\User;
use App\Request\Rules\PasswordRule;
use App\Service\UserService;

use function App\Helper\user;

class UserRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['required_with:role_ids', 'integer', 'exists:roles,id'],
            'dept_ids' => ['nullable', 'array'],
            'dept_ids.*' => ['required_with:dept_ids', 'integer', 'exists:depts,id'],
            'status' => ['nullable', 'integer', 'in:1,2'],
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            'username' => ['required', 'max:20', 'unique:users'],
            'password' => ['required', 'min:6', new PasswordRule()],
            'phone' => ['telephone_number'],
            'dept_ids' => ['nullable', 'array'],
            'dept_ids.*' => ['required_with:dept_ids', 'integer', 'exists:depts,id'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'role_ids' => ['required'],
            'remark' => ['max:255'],
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function updateRules(): array
    {
        return [
            'username' => ['required', 'max:20'],
            'phone' => ['telephone_number'],
            'dept_ids' => ['nullable', 'array'],
            'dept_ids.*' => ['required_with:dept_ids', 'integer', 'exists:depts,id'],
            'org_id' => ['required', 'integer', 'exists:organizations,id'],
            'role_ids' => ['required'],
            'remark' => ['max:255'],
        ];
    }

    /**
     * 修改状态数据验证规则
     * return array.
     */
    public function changeStatusRules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', 'integer', 'in:1,2'],
        ];
    }

    /**
     * 修改密码验证规则
     * return array.
     */
    public function modifyPasswordRules(): array
    {
        return [
            'newPassword' => ['required', 'min:6', 'confirmed', new PasswordRule()],
            'newPassword_confirmation' => 'required|string',
            'oldPassword' => ['required', function ($attribute, $value, $fail) {
                $service = di()->get(UserService::class);
                /**
                 * @var User $model
                 */
                $model = $service->dao->getModel()::find(user()->getId(), ['password']);
                if (! $service->dao->checkPass($value, $model->password)) {
                    $fail(ErrorCode::USER_PASSWORD_ERROR->name);
                }
            }],
        ];
    }

    /**
     * 设置用户首页数据验证规则.
     */
    public function setHomePageRules(): array
    {
        return [
            'id' => 'required',
            'dashboard' => 'required',
        ];
    }

    /**
     * 登录规则.
     * @return string[]
     */
    public function loginRules(): array
    {
        return [
            'username' => 'required|max:20',
            'password' => 'required|min:6',
        ];
    }

    /**
     * 更改用户资料验证规则.
     */
    public function updateInfoRules(): array
    {
        return [
            'phone' => 'telephone_number',
            'remark' => ['max:255'],
        ];
    }

    /**
     * 删除（可批量）.
     */
    public function deleteRules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * 删除（可批量）.
     */
    public function realDeleteRules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * 删除（可批量）.
     */
    public function recoveryRules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'ids.*' => '用户ID',
            'id' => '用户ID',
            'username' => '用户名',
            'password' => '用户密码',
            'dashboard' => '用户后台首页',
            'oldPassword' => '旧密码',
            'newPassword' => '新密码',
            'newPassword_confirmation' => '确认密码',
            'status' => '用户状态',
            'dept_ids' => '部门ID',
            'dept_ids.*' => '部门ID',
            'role_ids' => '角色列表',
            'role_ids.*' => '角色列表',
            'phone' => '手机',
            'email' => '邮箱',
            'remark' => '备注',
            'signed' => '个人签名',
        ];
    }
}
