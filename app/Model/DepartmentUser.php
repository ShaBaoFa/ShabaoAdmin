<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Model;

use Carbon\Carbon;

/**
 * @property int $user_id
 * @property int $department_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class DepartmentUser extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'department_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'department_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'department_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
