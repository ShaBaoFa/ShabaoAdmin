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
 * @property int $user_id 用户主键
 * @property int $post_id 岗位主键
 */
class PostUser extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'post_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'post_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'post_id' => 'integer'];
}
