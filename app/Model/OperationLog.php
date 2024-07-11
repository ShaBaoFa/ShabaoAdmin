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
 * @property int $id 
 * @property string $username 
 * @property string $method 
 * @property string $router 
 * @property string $service_name 
 * @property string $ip 
 * @property string $ip_location 
 * @property string $request_data 
 * @property string $response_code 
 * @property string $response_data 
 * @property int $created_by 
 * @property int $updated_by 
 * @property Carbon $created_at 
 * @property Carbon $updated_at 
 * @property string $deleted_at 
 * @property string $remark 
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
