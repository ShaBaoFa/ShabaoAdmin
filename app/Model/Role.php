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
use App\Model\Pivots\DepartmentRole;
use App\Model\Pivots\MenuRole;
use App\Model\Pivots\RoleUser;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsToMany;

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
 * @property-read null|Collection|Department[] $depts 
 * @property-read null|Collection|Menu[] $menus 
 * @property-read null|Collection|User[] $users 
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

    // 平台方
    public const PLATFORM_SCOPE = 6;

    // 主办方
    public const ORGANIZER_SCOPE = 7;

    // 参展商
    public const EXHIBITOR_SCOPE = 8;

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
