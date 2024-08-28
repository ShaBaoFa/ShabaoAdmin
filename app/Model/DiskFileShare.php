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

/**
 * @property int $id 主键
 * @property string $name 分享包名称
 * @property int $created_by 创建者ID
 * @property int $updated_by 更新者ID
 * @property string $share_link 分享链接的唯一标识符
 * @property int $permission 分享权限（例如：1.查看+下载、2.只查看）
 * @property string $share_password 分享密码（可为空）
 * @property string $expires_at 到期时间
 * @property int $view_count 查看次数
 * @property int $download_count 下载次数
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property null|Collection|DiskFile[] $diskFiles
 * @property null|Collection|User[] $shareWith
 */
class DiskFileShare extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'disk_file_shares';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'created_by', 'updated_by', 'share_link', 'permission', 'share_password', 'expires_at', 'view_count', 'download_count', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'created_by' => 'integer', 'updated_by' => 'integer', 'view_count' => 'integer', 'download_count' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'permission' => 'integer'];

    /**
     * 定义与 User 的多对多关系.
     */
    public function shareWith(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'disk_file_share_user',
            'share_id',
            'user_id'
        );
    }

    public function diskFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            DiskFile::class,
            'disk_file_share_file',
            'share_id',
            'file_id'
        );
    }
}
