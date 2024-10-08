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
 * @property int $id 主键
 * @property string $username 用户名
 * @property string $ip 登录IP地址
 * @property string $ip_location IP所属地
 * @property string $os 操作系统
 * @property string $browser 浏览器
 * @property int $status 登录状态 (1成功 2失败)
 * @property string $message 提示消息
 * @property string $login_time 登录时间
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
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
    protected array $fillable = ['id', 'username', 'ip', 'ip_location', 'os', 'browser', 'status', 'message', 'login_time', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'status' => 'integer'];
}
