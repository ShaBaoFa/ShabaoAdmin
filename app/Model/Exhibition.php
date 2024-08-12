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
 * @property int $status 
 * @property int $sort 
 * @property int $created_by 
 * @property int $updated_by 
 * @property Carbon $created_at 
 * @property Carbon $updated_at 
 * @property string $deleted_at 
 * @property string $remark 
 */
class Exhibition extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exhibitions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
