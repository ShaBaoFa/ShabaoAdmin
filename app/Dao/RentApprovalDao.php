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

namespace App\Dao;

use App\Base\BaseDao;
use App\Constants\AuditCode;
use App\Model\UserRentApproval;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class RentApprovalDao extends BaseDao
{
    /**
     * @var UserRentApproval
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = UserRentApproval::class;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {

        $query->when(
            Arr::get($params, 'audit_status'),
            fn (Builder $query, $auditStatus) => $query->where('audit_status', $auditStatus)
        );

        $query->when(
            Arr::get($params, 'audit_organization_id'),
            fn (Builder $query, $audit_organization_id) => $query->where('audit_organization_id', $audit_organization_id)
        );

        $query->when(
            Arr::get($params, 'exh_lib_obj_name'),
            fn (Builder $query, $exh_lib_obj_name) => $query->where('exh_lib_obj_name', 'like', '%' . $exh_lib_obj_name . '%')
        );

        $query->when(
            Arr::get($params, 'created_at'),
            function (Builder $query, $createdAt) {
                if (is_array($createdAt) && count($createdAt) === 2) {
                    $query->whereBetween(
                        'created_at',
                        [$createdAt[0] . ' 00:00:00', $createdAt[1] . ' 23:59:59']
                    );
                }
            }
        );

        return $query;
    }

    public function changeAuditStatus(int $id, int $auditStatus, string $refuse_reason): bool
    {
        /**
         * @var UserRentApproval $model
         */
        $model = $this->model::find($id);
        if (! in_array($auditStatus, $this->auditMap($model->audit_status))) {
            return false;
        }
        $model->audit_status = $auditStatus;
        $model->refuse_reason = $refuse_reason;
        $model->save();
        return true;
    }

    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    public function cancelDownloadApproval(int $id): bool
    {
        /**
         * @var UserRentApproval $model
         */
        $model = $this->model::find($id);
        if (! in_array(AuditCode::CANCEL->value, $this->auditMap($model->audit_status))) {
            return false;
        }
        $model->audit_status = AuditCode::CANCEL->value;
        $model->save();
        return true;
    }

    /**
     * 流程支持的操作.
     * @param mixed $modelAuditStatus
     */
    private function auditMap(int $modelAuditStatus): array
    {
        // 比如 审查中的状态只能被同意或者拒绝
        // 同意和已拒绝的则不可被修改
        return match ($modelAuditStatus) {
            AuditCode::IN_AUDIT->value => [
                AuditCode::PASS->value, AuditCode::NOT_PASS->value, AuditCode::CANCEL->value,
            ],
            default => [],
        };
    }
}
