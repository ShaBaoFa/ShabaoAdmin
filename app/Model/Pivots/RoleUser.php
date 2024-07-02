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

namespace App\Model\Pivots;

use App\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\Pivot;

/**
 * @property int $user_id
 * @property int $role_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class RoleUser extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'role_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'role_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
