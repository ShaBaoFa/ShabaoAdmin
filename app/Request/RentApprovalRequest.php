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
use App\Constants\AuditCode;
use Hyperf\Validation\Rule;

class RentApprovalRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'ids.*' => ['integer', 'exists:user_rent_approvals,id'],
            'id' => ['integer', 'exists:user_rent_approvals,id'],
        ];
    }

    /**
     * 目录数据验证规则
     * return array.
     */
    public function changeAuditStatusRules(): array
    {
        return [
            'id' => ['integer', 'exists:user_rent_approvals,id'],
            'audit_status' => ['integer', Rule::in([
                AuditCode::PASS->value,
                AuditCode::NOT_PASS->value,
            ])],
            'refuse_reason' => 'required_if:audit_status,' . AuditCode::NOT_PASS->value,
        ];
    }

    public function createApprovalRules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'reason' => 'required|string|max:255',
            'exh_lib_obj_id' => 'required|integer|exists:exh_lib_objs,id',
            'rent_start_at' => 'required|date',
            'rent_end_at' => 'required|date|after:start_at',
            'contact_name' => 'required|string|max:50',
            'contact_phone' => 'required|string|max:20',
            'project_name' => 'required|string|max:50',
        ];
    }

    public function myDownloadApprovalRules(): array
    {
        return [];
    }
}
