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

class CommentRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
            'id' => 'nullable|exists:comments,id',
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function saveRules(): array
    {
        return [
            'body' => 'required|max:255',
            'commentable_id' => ['required', 'int'],
            'commentable_type' => ['required', 'int', 'min:1', 'max:1'],
            'sent_to' => ['required_with:parent_id', 'int', 'exists:users,id'],
            'parent_id' => ['nullable', 'int', 'exists:comments,id'],
        ];
    }

    public function indexRules(): array
    {
        return [
            'commentable_id' => ['required', 'int'],
            'commentable_type' => ['required', 'int', 'min:1', 'max:1'],
            'parent_id' => ['nullable', 'int', 'exists:comments,id'],
        ];
    }
    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'id' => '评论',
            'body' => '评论内容',
        ];
    }
}
