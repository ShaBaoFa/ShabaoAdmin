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
 * @property int $id 主键
 * @property int $parent_id 父级
 * @property int $level 等级
 * @property string $name 名称
 * @property string $initial 首字母
 * @property string $pinyin 拼音
 * @property string $citycode 城市编码
 * @property string $adcode 区域编码
 * @property string $lng_lat 中心经纬度
 * @property string $deleted_at 删除时间
 */
class Region extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'region';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'level', 'name', 'initial', 'pinyin', 'citycode', 'adcode', 'lng_lat', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'parent_id' => 'integer', 'level' => 'integer'];
}
