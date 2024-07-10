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
 * @property string $username
 * @property int $status
 * @property string $phone
 * @property string $login_ip
 * @property Carbon $login_time
 * @property int $created_by
 * @property int $updated_by
 * @property string $remark
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property string $user_type
 * @property null|Collection|Role[] $roles
 * @property mixed $password
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
    protected array $fillable = ['id', 'username', 'password', 'status', 'phone', 'login_ip', 'login_time', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at', 'user_type'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'username' => 'string', 'password' => 'string', 'status' => 'integer', 'login_ip' => 'string', 'login_time' => 'datetime', 'created_by' => 'integer', 'updated_by' => 'integer', 'remark' => 'string', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime'];

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
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'dept_id');
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
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }
}
