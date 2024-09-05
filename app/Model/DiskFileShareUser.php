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
 * @property int $share_id 分享包主键
 * @property int $user_id 接收分享的用户ID
 */
class DiskFileShareUser extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'disk_file_share_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['share_id', 'user_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['share_id' => 'integer', 'user_id' => 'integer'];
}
