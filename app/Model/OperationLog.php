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

/**
 * @property int $id 主键
 * @property string $username 用户名
 * @property string $method 请求方式
 * @property string $router 请求路由
 * @property string $service_name 业务名称
 * @property string $ip 请求IP地址
 * @property string $ip_location IP所属地
 * @property string $request_data 请求数据
 * @property string $response_code 响应状态码
 * @property string $response_data 响应数据
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $remark 备注
 */
class OperationLog extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'operation_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'method', 'router', 'service_name', 'ip', 'ip_location', 'request_data', 'response_code', 'response_data', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
