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
 * @property int $exh_lib_obj_id 展项主键
 * @property int $tag_id 标签ID
 */
class ExhLibObjTag extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exh_lib_obj_tag';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['exh_lib_obj_id', 'tag_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['exh_lib_obj_id' => 'integer', 'tag_id' => 'integer'];
}
