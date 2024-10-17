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

use function App\Helper\user;

/**
 * @property int $id
 * @property string $title 标题
 * @property string $author 作者
 * @property string $phone 手机
 * @property string $email 邮箱
 * @property string $profile 专区简介
 * @property int $save_dir_id 存储文件夹ID
 * @property string $redirect_url 跳转地址
 * @property int $type 展项类型 (1虚拟展项素材 2实体展项素材 3平台展项素材)
 * @property int $lib_type 大区类型 (1战新 2行业 3主题 4专场)
 * @property int $lib_area_type 子分区分类
 * @property int $status 状态 (1正常 2停用)
 * @property int $audit_status 审核 (1审核中 2通过 3拒绝)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property int $star_count 点赞次数
 * @property int $collect_count 收藏次数
 * @property null|Collection|ExhLibTag[] $tags
 * @property null|Collection|UploadFile[] $covers
 * @property null|Collection|UploadFile[] $files
 * @property null|Collection|Region[] $share_regions
 * @property null|Collection|User[] $starUsers
 * @property null|Collection|User[] $pickUsers
 * @property null|Collection|User[] $favoriteUsers
 */
class ExhLibObj extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exh_lib_objs';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'author', 'phone', 'email', 'profile', 'save_dir_id', 'redirect_url', 'type', 'lib_type', 'lib_area_type', 'status', 'audit_status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at', 'star_count', 'collect_count'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'type' => 'integer', 'lib_type' => 'integer', 'lib_area_type' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'audit_status' => 'integer', 'save_dir_id' => 'integer', 'star_count' => 'integer', 'collect_count' => 'integer'];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ExhLibTag::class, 'exh_lib_obj_tag', 'exh_lib_obj_id', 'tag_id');
    }

    public function covers(): BelongsToMany
    {
        return $this->belongsToMany(UploadFile::class, 'exh_lib_obj_cover_upload_file', 'exh_lib_obj_id', 'upload_file_id');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(UploadFile::class, 'exh_lib_obj_upload_file', 'exh_lib_obj_id', 'upload_file_id');
    }

    public function share_regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'exh_lib_obj_share_region', 'exh_lib_obj_id', 'share_region_id');
    }

    public function starUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_star_obj', 'obj_id', 'user_id');
    }

    public function pickUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_pick_obj', 'obj_id', 'user_id');
    }

    public function collectUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_obj', 'obj_id', 'user_id');
    }
}
