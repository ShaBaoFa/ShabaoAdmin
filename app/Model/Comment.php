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
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\Relations\MorphTo;

/**
 * @property int $id 评论ID
 * @property int $parent_id 父级ID
 * @property int $star_count 点赞数
 * @property string $body 评论内容
 * @property int $commentable_id 评论对象ID
 * @property string $commentable_type 评论对象类型
 * @property int $sent_to 收件人
 * @property int $status 状态 (1正常 2停用)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property null|Collection|Comment[] $subComments
 * @property null|Model $commentable
 * @property null|User $createdBy
 * @property null|User $sentTo
 */
class Comment extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'comments';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'star_count', 'body', 'commentable_id', 'commentable_type', 'sent_to', 'status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'parent_id' => 'integer', 'star_count' => 'integer', 'commentable_id' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'sent_to' => 'integer'];

    /**
     * 关联评论对象.
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 关联创建用户.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * 关联收件人.
     */
    public function sentTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_to', 'id');
    }

    public function subComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }
}
