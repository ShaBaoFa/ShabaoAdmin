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
 * @property int $id
 * @property string $title 标题
 * @property string $author 作者
 * @property int $lib_area_type 子分区分类
 * @property string $profile 文章简介
 * @property string $content 文章内容
 * @property int $audit_organization_id 审批组织
 * @property int $status 状态 (1正常 2停用)
 * @property int $audit_status 审核 (1审核中 2通过 3拒绝)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property string $published_at 发布时间
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class News extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'news';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'author', 'lib_area_type', 'profile', 'content', 'audit_organization_id', 'status', 'audit_status', 'sort', 'created_by', 'updated_by', 'remark', 'published_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'lib_area_type' => 'integer', 'audit_organization_id' => 'integer', 'status' => 'integer', 'audit_status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
