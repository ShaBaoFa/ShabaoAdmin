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
use Hyperf\Database\Model\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $level
 * @property string $name
 * @property string $address
 * @property string $legal_person
 * @property string $phone
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property string $remark
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
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
    protected array $fillable = ['id', 'parent_id', 'level', 'name', 'address', 'legal_person', 'phone', 'status', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'status' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'parent_id' => 'integer'];

    /**
     * 通过中间表关联用户.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user', 'organization_id', 'user_id');
    }
}
