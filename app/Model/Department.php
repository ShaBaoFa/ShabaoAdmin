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
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $level
 * @property string $name
 * @property string $leader
 * @property string $phone
 * @property int $status
 * @property int $sort
 * @property int $created_by
 * @property int $updated_by
 * @property string $remark
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property null|Collection|User[] $users
 * @property null|Collection|Role[] $roles
 * @property null|Collection|Organization[] $organizations
 */
class Department extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'departments';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'level', 'name', 'leader', 'phone', 'status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'parent_id' => 'integer', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'sort' => 'integer'];

    /**
     * 通过中间表关联用户.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user', 'department_id', 'user_id');
    }

    /**
     * 通过中间表获取角色.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'department_role', 'department_id', 'role_id');
    }

    /**
     * 通过中间表获取组织.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'department_organization', 'department_id', 'organization_id');
    }
}
