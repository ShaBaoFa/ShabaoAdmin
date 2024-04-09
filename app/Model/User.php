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
 * @property string $account
 * @property string $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class User extends Model implements Authenticatable
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'account', 'password', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function getId(): int
    {
        return self::getKey();
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::findFromCache($key);
    }
}
