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

    public function myDownloadApprovalRules(): array
    {
        return [];
    }
}
