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
 * @property int $storage_mode 存储模式 (1.本地 2.阿里云OSS 3.FTP 4.内存 5.S3 6.Minio 7.七牛云 8.腾讯云COS)
 * @property string $origin_name 原文件名
 * @property string $object_name 新文件名
 * @property string $hash 文件hash
 * @property string $mime_type 资源类型
 * @property string $storage_path 存储目录
 * @property string $suffix 文件后缀
 * @property int $size_byte 字节数
 * @property string $size_info 文件大小
 * @property string $url url地址
 * @property int $status 状态 (1完成 2未完成)
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 */
class UploadFile extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'upload_files';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'storage_mode', 'origin_name', 'object_name', 'hash', 'mime_type', 'storage_path', 'suffix', 'size_byte', 'size_info', 'url', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'storage_mode' => 'integer', 'size_byte' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'status' => 'integer'];
}
