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
use Hyperf\Database\Model\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $level
 * @property string $name
 * @property string $code
 * @property string $icon
 * @property string $route
 * @property string $component
 * @property string $redirect
 * @property int $is_hidden
 * @property string $type
 * @property int $status
 * @property int $sort
 * @property int $created_by
 * @property int $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property string $remark
 */
class Menu extends BaseModel
{
    /**
     * 类型.
     */
    public const LINK = 'L';

    public const IFRAME = 'I';

    public const MENUS_LIST = 'M';

    public const BUTTON = 'B';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'menus';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'level', 'name', 'code', 'icon', 'route', 'component', 'redirect', 'is_hidden', 'type', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'parent_id' => 'integer', 'is_hidden' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * 通过中间表获取角色.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_role', 'menu_id', 'role_id');
    }
}
