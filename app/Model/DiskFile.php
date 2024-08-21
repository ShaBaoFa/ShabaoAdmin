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

/**
 * @property int $id 主键
 * @property string $name 文件(文件夹)名
 * @property string $level 文件(文件夹)路径
 * @property string $hash 文件hash
 * @property string $suffix 文件后缀
 * @property int $parent_id 父ID
 * @property int $size_byte 字节数
 * @property string $size_info 文件大小
 * @property int $is_folder 是否文件夹
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 */
class DiskFile extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'disk_files';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'level', 'hash', 'suffix', 'parent_id', 'size_byte', 'size_info', 'is_folder', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'parent_id' => 'integer', 'size_byte' => 'integer', 'is_folder' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
