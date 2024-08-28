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

/**
 * @property int $share_id 分享包主键
 * @property int $file_id 接收分享的文件ID
 */
class DiskFileShareFile extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'disk_file_share_file';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['share_id', 'file_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['share_id' => 'integer', 'file_id' => 'integer'];
}
