<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Model;

use Carbon\Carbon;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $status
 * @property string $login_ip
 * @property Carbon $login_time
 * @property int $created_by
 * @property int $updated_by
 * @property string $remark
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class User extends Model implements Authenticatable
{
    public const STATUS_NORMAL = 1;

    public const STATUS_DISABLE = 2;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'password', 'status', 'login_ip', 'login_time', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'username' => 'string', 'password' => 'string', 'dept_id' => 'integer', 'status' => 'integer', 'login_ip' => 'string', 'login_time' => 'datetime', 'created_by' => 'integer', 'updated_by' => 'integer', 'remark' => 'string', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime'];

    public function getId(): int
    {
        return self::getKey();
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::findFromCache($key);
    }

    public static function passwordVerify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }
}
