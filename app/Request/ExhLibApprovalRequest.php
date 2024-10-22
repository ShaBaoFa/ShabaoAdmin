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

class ExhLibApprovalRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'ids.*' => ['integer', 'exists:exh_lib_areas,id'],
            'id' => ['integer', 'exists:exh_lib_areas,id'],
        ];
    }

    /**
     * 目录数据验证规则
     * return array.
     */
    public function changeDownloadAuditStatusRules(): array
    {
        return [
            'id' => ['integer', 'exists:user_download_approvals,id'],
            'audit_status' => ['integer', Rule::in([
                AuditCode::PASS->value,
                AuditCode::NOT_PASS->value,
            ])],
            'refuse_reason' => 'required_if:audit_status,' . AuditCode::NOT_PASS->value,
        ];
    }

    public function changeUploadAuditStatusRules(): array
    {
        return [
            'id' => ['integer', 'exists:exh_lib_objs,id'],
            'audit_status' => ['integer', Rule::in([
                AuditCode::PASS->value,
                AuditCode::NOT_PASS->value,
            ])],
            'refuse_reason' => 'required_if:audit_status,' . AuditCode::NOT_PASS->value,
        ];
    }

    public function downloadIndexRules(): array
    {
        return [
            'exh_lib_obj_name' => 'string',
            'type' => ['integer', Rule::in(1, 2, 3)], // 展项类型 (1虚拟展项素材 2实体展项素材 3平台展项素材)
        ];
    }

    public function uploadIndexRules(): array
    {
        return [
            'title' => 'string',
            'type' => ['integer', Rule::in(1, 2, 3)], // 展项类型 (1虚拟展项素材 2实体展项素材 3平台展项素材)
        ];
    }

    public function createDownloadApprovalRules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'reason' => 'required|string|max:255',
            'exh_lib_obj_id' => 'required|integer|exists:exh_lib_objs,id',
        ];
    }

    public function cancelDownloadApprovalRules(): array
    {
        return [
            'id' => 'required|integer|exists:user_download_approvals,id',
        ];
    }

    public function myDownloadApprovalRules(): array
    {
        return [];
    }
}
