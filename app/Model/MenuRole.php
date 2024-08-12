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
 * @property int $role_id 
 * @property int $menu_id 
 */
class MenuRole extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'menu_role';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['role_id', 'menu_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['role_id' => 'integer', 'menu_id' => 'integer'];
}
