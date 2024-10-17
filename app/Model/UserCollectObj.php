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
 * @property int $obj_id 展项主键
 * @property int $user_id 用户主键
 */
class UserCollectObj extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_collect_obj';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['obj_id', 'user_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['obj_id' => 'integer', 'user_id' => 'integer'];
}
