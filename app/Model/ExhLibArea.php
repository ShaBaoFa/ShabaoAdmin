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
 * @property string $name 专区名称
 * @property string $icon 专区icon
 * @property string $profile 专区简介
 * @property int $lib_type 大区类型 (1战新 2行业 3主题 4专场)
 * @property int $status 状态 (1正常 2停用)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class ExhLibArea extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exh_lib_areas';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'icon', 'profile', 'lib_type', 'status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'lib_type' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
