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

use App\Base\BaseModel;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\Relations\HasOneThrough;

/**
 * @property int $id 主键
 * @property int $content_type 内容类型
 * @property string $title 消息标题
 * @property int $send_by 发送人
 * @property int $receive_by 接受人(私信需填)
 * @property string $content 消息内容
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property null|User $receiveUser
 * @property null|User $sendUser
 * @property null|Collection|User[] $receiveUsers
 */
class Message extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'messages';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'content_type', 'title', 'send_by', 'receive_by', 'content', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'content_type' => 'integer', 'send_by' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'receive_by' => 'integer'];

    /**
     * 关联发送人.
     */
    public function sendUser(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'send_by');
    }

    public function receiveUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class, // 最终你想获取的模型
            MessageReceiver::class, // 中间模型
            'message_id', // MessageReceiver 表中的外键
            'id', // User 表中的本地键
            'id', // Message 表中的本地键
            'receiver_id' // MessageReceiver 表中的本地键
        );
    }

    /**
     * 关联接收人中间表.
     */
    public function receiveUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_receivers', 'message_id', 'receiver_id')
            ->withPivot('read_status'); // 读取额外的字段
    }
}
