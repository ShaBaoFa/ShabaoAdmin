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
 * @property int $id 主键
 * @property int $parent_id 父ID
 * @property string $level 组级集合
 * @property string $name 菜单名称
 * @property string $code 菜单标识代码
 * @property string $icon 菜单图标
 * @property string $route 路由地址
 * @property string $component 组件路径
 * @property string $redirect 跳转地址
 * @property int $is_hidden 是否隐藏 (1是 2否)
 * @property string $type 菜单类型, (M菜单 B按钮 L链接 I iframe)
 * @property int $status 状态 (1正常 2停用)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 * @property null|Collection|Role[] $roles
 */
class Menu extends BaseModel
{
    /**
     * 类型.
     */
    /**
     * 链接.
     */
    public const LINK = 'L';

    /**
     * IFRAME.
     */
    public const IFRAME = 'I';

    /**
     * 菜单.
     */
    public const MENUS_LIST = 'M';

    /**
     * 按钮.
     */
    public const BUTTON = 'B';

    /**
     * 生成Btn.
     */
    public const GEN_BTN = 1;

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
