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
 * @property string $username 账号
 * @property string $nickname 昵称
 * @property string $avatar 头像
 * @property string $post 岗位
 * @property string $dept 部门
 * @property int $status 状态 (1正常 2停用)
 * @property string $phone 手机
 * @property string $login_ip 最后登陆IP
 * @property Carbon $login_time 最后登陆时间
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property string $user_type 用户类型：(100系统用户)
 * @property int $point 个人积分
 * @property null|Collection|DiskFileShare[] $sharedFiles
 * @property null|Collection|Role[] $roles
 * @property null|Collection|Department[] $depts
 * @property null|Collection|Organization[] $organizations
 * @property mixed $password 密码
 */
class User extends BaseModel
{
    public const STATUS_NORMAL = 1;

    public const STATUS_DISABLE = 2;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    protected array $hidden = ['password', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'nickname', 'avatar', 'post', 'dept', 'password', 'status', 'phone', 'login_ip', 'login_time', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at', 'user_type', 'point'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'username' => 'string', 'password' => 'string', 'status' => 'integer', 'login_ip' => 'string', 'login_time' => 'datetime', 'created_by' => 'integer', 'updated_by' => 'integer', 'remark' => 'string', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime', 'point' => 'integer'];

    /**
     * 通过中间表关联角色.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * 通过中间表关联部门.
     */
    public function depts(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id');
    }

    /**
     * 通过中间表关联组织.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_user', 'user_id', 'organization_id');
    }

    public function getId(): int
    {
        return self::getKey();
    }

    public static function passwordVerify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * password 加密.
     * @param mixed $value
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    public function sharedFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            DiskFileShare::class,
            'disk_file_share_user',
            'user_id',
            'share_id'
        );
    }
}
