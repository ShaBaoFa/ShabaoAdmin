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
 * @property string $name 角色名称
 * @property string $code 角色代码
 * @property int $type 角色类型(1公共角色 2UGC角色)
 * @property int $data_scope 数据范围（1：全部数据权限 2：自定义数据权限 3：本部门数据权限 4：本部门及以下数据权限 5：本人数据权限）
 * @property int $status 状态 (1正常 2停用)
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property null|Collection|Department[] $depts
 * @property null|Collection|Menu[] $menus
 * @property null|Collection|User[] $users
 */
class Role extends BaseModel
{
    // 所有
    public const ALL_SCOPE = 1;

    // 自定义
    public const CUSTOM_SCOPE = 2;

    // 本组织
    public const SELF_ORGANIZATION_SCOPE = 3;

    // 需组织审核
    public const ORGANIZATION_AUDIT_SCOPE = 4;

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

    /**
     * 通过中间表获取菜单.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_role', 'role_id', 'menu_id');
    }

    /**
     * 通过中间表获取用户.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    public function depts(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_role', 'role_id', 'department_id');
    }
}
