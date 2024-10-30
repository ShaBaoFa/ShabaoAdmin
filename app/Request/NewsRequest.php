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

class NewsRequest extends BaseFormRequest
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
            'title' => 'required|max:30',
            'author' => 'required|string|max:30',
            'lib_area_type' => ['required', 'int'],
            'profile' => ['required', 'string'],
            'content' => ['required', 'string'],
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '文章',
            'title' => '标题',
            'author' => '作者',
            'content' => '内容',
            'status' => '状态',
            'audit_status' => '审核状态',
        ];
    }
}
