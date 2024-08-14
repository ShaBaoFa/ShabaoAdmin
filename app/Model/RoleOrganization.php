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
 * @property int $organization_id 组织主键
 * @property int $role_id 角色主键
 */
class RoleOrganization extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_organization';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['organization_id', 'role_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['organization_id' => 'integer', 'role_id' => 'integer'];
}
