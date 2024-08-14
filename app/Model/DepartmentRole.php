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
 * @property int $role_id 角色主键
 * @property int $department_id 部门主键
 */
class DepartmentRole extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'department_role';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['role_id', 'department_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['role_id' => 'integer', 'department_id' => 'integer'];
}
