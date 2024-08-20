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
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id
 * @property int $parent_id 父ID
 * @property int $super_admin_id 企业超管ID
 * @property string $level 组级集合
 * @property string $name 组织名称
 * @property string $address 组织地址
 * @property string $legal_person 法人
 * @property string $phone 组织电话
 * @property int $status 状态 (1正常 2停用)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property null|Collection|User[] $users
 * @property null|Collection|Department[] $depts
 * @property null|User $superAdmin
 */
class Organization extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'organizations';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'super_admin_id', 'level', 'name', 'address', 'legal_person', 'phone', 'status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'parent_id' => 'integer', 'super_admin_id' => 'integer', 'sort' => 'integer'];

    /**
     * 通过中间表关联用户.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user', 'organization_id', 'user_id');
    }

    /**
     * 通过中间表关联部门.
     */
    public function depts(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_organization', 'organization_id', 'department_id');
    }

    /**
     * 组织超级管理员.
     */
    public function superAdmin(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'super_admin_id');
    }
}
