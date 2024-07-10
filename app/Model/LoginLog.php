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

/**
 * @property int $id
 * @property string $username
 * @property string $ip
 * @property string $ip_location
 * @property string $os
 * @property string $browser
 * @property int $status
 * @property string $message
 * @property string $login_time
 * @property string $remark
 */
class LoginLog extends BaseModel
{
    public const SUCCESS = 1;

    public const FAIL = 2;

    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'login_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'ip', 'ip_location', 'os', 'browser', 'status', 'message', 'login_time', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'status' => 'integer'];
}
