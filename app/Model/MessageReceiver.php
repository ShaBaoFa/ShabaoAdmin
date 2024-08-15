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

namespace App\Model;

use Hyperf\Database\Model\Relations\Pivot;

/**
 * @property int $message_id 队列消息主键
 * @property int $receiver_id 接收用户主键
 * @property int $read_status 已读状态 (1未读 2已读)
 */
class MessageReceiver extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'message_receivers';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['message_id', 'receiver_id', 'read_status'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['message_id' => 'integer', 'receiver_id' => 'integer', 'read_status' => 'integer'];
}
