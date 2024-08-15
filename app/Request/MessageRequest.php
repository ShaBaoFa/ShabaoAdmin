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

class MessageRequest extends BaseFormRequest
{
    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [
        ];
    }

    /**
     * 新增数据验证规则
     * return array.
     */
    public function sendPrivateMessageRules(): array
    {
        return [
            'receive_by' => 'required|int|exists:users,id',
            'content' => 'required|string',
        ];
    }

    /**
     * 更新数据验证规则
     * return array.
     */
    public function getPrivateConversationInfoRules(): array
    {
        return [
            'receive_by' => 'required|int|exists:users,id',
        ];
    }

    /**
     * 字段映射名称
     * return array.
     */
    public function attributes(): array
    {
        return [
            'receive_by' => '接受人ID',
            'content' => '信息内容',
            'title' => '信息标题',
        ];
    }
}
