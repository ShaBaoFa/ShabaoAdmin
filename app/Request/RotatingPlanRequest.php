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

class RotatingPlanRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'ids.*' => ['integer', 'exists:rotating_plans,id'],
            'id' => ['integer', 'exists:rotating_plans,id'],
        ];
    }

    public function saveRules(): array
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
