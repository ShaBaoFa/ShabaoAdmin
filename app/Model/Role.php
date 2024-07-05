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
class Role extends BaseModel
{
    // 所有
    public const ALL_SCOPE = 1;

    // 自定义
    public const CUSTOM_SCOPE = 2;

    // 本部门
    public const SELF_DEPT_SCOPE = 3;

    // 本部门及子部门
    public const DEPT_BELOW_SCOPE = 4;

    // 本人
    public const SELF_SCOPE = 5;
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
