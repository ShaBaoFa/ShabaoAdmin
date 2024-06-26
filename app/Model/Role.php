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
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $type
 * @property int $data_scope
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property string $remark
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class Role extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'roles';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'code', 'type', 'data_scope', 'status', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'type' => 'integer', 'data_scope' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
