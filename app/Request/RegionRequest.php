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

class RegionRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 目录数据验证规则
     * return array.
     */
    public function indexRules(): array
    {
        return [
            'keywords' => ['nullable', 'string', 'max:20'],
            'level' => ['nullable', 'int'],
            'parent_id' => ['nullable', 'int'],
        ];
    }
}
