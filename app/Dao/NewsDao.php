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
use App\Model\News;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

class NewsDao extends BaseDao
{
    /**
     * @var News
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = News::class;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'status'),
            fn (Builder $query, $status) => $query->where('status', $status)
        );
        $query->when(
            Arr::get($params, 'audit_status'),
            fn (Builder $query, $auditStatus) => $query->where('audit_status', $auditStatus)
        );

        $query->when(
            Arr::get($params, 'audit_organization_id'),
            fn (Builder $query, $audit_organization_id) => $query->where('audit_organization_id', $audit_organization_id)
        );

        $query->when(
            Arr::get($params, 'content'),
            fn (Builder $query, $content) => $query->where('content', 'like', '%' . $content . '%')
        );

        $query->when(
            Arr::get($params, 'title'),
            fn (Builder $query, $title) => $query->where('title', 'like', '%' . $title . '%')
        );

        $query->when(
            $keywords = Arr::get($params, 'keywords'),
            fn (Builder $query) => $query->where(function (Builder $builder) use ($keywords) {
                $builder->where('title', 'like', '%' . $keywords . '%')
                    ->orWhere('content', 'like', '%' . $keywords . '%')
                    ->orWhere('profile', 'like', '%' . $keywords . '%');
            })
        );

        $query->when(
            Arr::get($params, 'published_at'),
            function (Builder $query, $publishedAt) {
                if (is_array($publishedAt) && count($publishedAt) === 2) {
                    $query->whereBetween(
                        'published_at',
                        [$publishedAt[0] . ' 00:00:00', $publishedAt[1] . ' 23:59:59']
                    );
                }
            }
        );

        return $query;
    }

    public function changeAuditStatus(int $id, int $auditStatus, string $refuse_reason): bool
    {
        /**
         * @var News $model
         */
        $model = $this->model::find($id);
        if (! in_array($auditStatus, $this->auditMap($model->audit_status))) {
            return false;
        }
        $model->audit_status = $auditStatus;
        $model->remark = $refuse_reason;
        $model->save();
        return true;
    }

    public function save(array $data): mixed
    {
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        return $model->{$model->getKeyName()};
    }

    public function cancel(int $id): bool
    {
        /**
         * @var News $model
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
