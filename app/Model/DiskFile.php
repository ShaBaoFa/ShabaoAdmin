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
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\Database\Model\Relations\HasMany;

/**
 * @property int $id 主键
 * @property string $name 文件(文件夹)名
 * @property string $level 文件(文件夹)路径
 * @property string $hash 文件hash
 * @property string $suffix 文件后缀
 * @property int $parent_id 父ID
 * @property int $size_byte 字节数
 * @property string $size_info 文件大小
 * @property int $type (1: 文件夹 2: 文件)
 * @property int $file_type (21: 图片 22: 视频 23: 音频 24: 文档 25: 其他)
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property int $is_deleted 是否删除
 * @property null|DiskFile $parent
 * @property null|Collection|DiskFile[] $children
 * @property null|Collection|DiskFileShare[] $shares
 */
class DiskFile extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'disk_files';

    protected array $hidden = [''];

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'level', 'hash', 'suffix', 'parent_id', 'size_byte', 'size_info', 'type', 'file_type', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark', 'is_deleted'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'parent_id' => 'integer', 'size_byte' => 'integer', 'is_folder' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'type' => 'integer', 'file_type' => 'integer', 'is_deleted' => 'integer'];

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function getParentId(): int
    {
        return $this->parent_id;
    }

    public function getSizeByte(): int
    {
        return $this->size_byte;
    }

    public function getSizeInfo(): string
    {
        return $this->size_info;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getFileType(): int
    {
        return $this->file_type;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DiskFile::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DiskFile::class, 'parent_id', 'id');
    }

    public function shares(): BelongsToMany
    {
        return $this->belongsToMany(
            DiskFileShare::class,
            'disk_file_share_file',
            'file_id',
            'share_id'
        );
    }
}
