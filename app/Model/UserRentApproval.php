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
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id 主键
 * @property int $audit_organization_id 审批组织
 * @property int $user_id 申请人主键
 * @property string $name 申请人名字
 * @property string $reason 申请理由
 * @property string $project_name 项目名称
 * @property string $rent_start_at 租用开始日期
 * @property string $rent_end_at 租用结束日期
 * @property string $contact_name 联系人
 * @property string $contact_phone 联系电话
 * @property int $exh_lib_obj_id 展项主键
 * @property string $exh_lib_obj_name 展项名字
 * @property string $cover 展项封面
 * @property string $refuse_reason 拒绝理由
 * @property int $status 状态 (1正常 2停用)
 * @property int $audit_status 审核 (1审核中 2通过 3拒绝,4取消)
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property string $remark 备注
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property null|ExhLibObj $exhibitionLibObj
 */
class UserRentApproval extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_rent_approvals';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'audit_organization_id', 'user_id', 'name', 'reason', 'project_name', 'rent_start_at', 'rent_end_at', 'contact_name', 'contact_phone', 'exh_lib_obj_id', 'exh_lib_obj_name', 'cover', 'refuse_reason', 'status', 'audit_status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'int', 'audit_organization_id' => 'integer', 'user_id' => 'integer', 'exh_lib_obj_id' => 'integer', 'status' => 'integer', 'audit_status' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function exhibitionLibObj(): BelongsTo
    {
        return $this->belongsTo(ExhLibObj::class, 'exh_lib_obj_id', 'id');
    }
}
