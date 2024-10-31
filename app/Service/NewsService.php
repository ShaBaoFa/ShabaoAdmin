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

namespace App\Service;

use App\Base\BaseService;
use App\Constants\AuditCode;
use App\Constants\ErrorCode;
use App\Dao\NewsDao;
use App\Dao\UserDao;
use App\Exception\BusinessException;
use Hyperf\Collection\Arr;

class NewsService extends BaseService
{
    /**
     * @var NewsDao
     */
    public $dao;

    public function __construct(NewsDao $dao)
    {
        $this->dao = $dao;
    }

    public function save(array $data): mixed
    {
        $data = $this->handleData($data);
        return parent::save($data);
    }

    public function update(mixed $id, array $data): bool
    {
        Arr::set($data, 'audit_status', AuditCode::IN_AUDIT->value);
        $data = $this->handleData($data);
        return parent::update($id, $data);
    }

    public function info(int $id): array
    {
        $model = $this->find($id);
        ! $model && throw new BusinessException(ErrorCode::NOT_FOUND);
        return $model->toArray();
    }

    public function handleData($data): array
    {
        if (! Arr::has($data, 'published_at')) {
            Arr::set($data, 'published_at', date('Y-m-d H:i:s'));
        }
        $userDao = di()->get(UserDao::class);
        $auditOrganizationId = $userDao->getParentOrganization();
        Arr::set($data, 'audit_organization_id', $auditOrganizationId);
        if ($auditOrganizationId == 0) {
            Arr::set($data, 'audit_status', AuditCode::PASS->value);
        }
        return $data;
    }

    public function changeAuditStatus(int $id, int $auditStatus, string $refuse_reason = ''): bool
    {
        if (! $this->find($id)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        if (! $this->dao->changeAuditStatus($id, $auditStatus, $refuse_reason)) {
            throw new BusinessException(ErrorCode::NOT_SUPPORT);
        }
        return true;
    }

    public function cancel(int $id): bool
    {
        if (! $this->dao->cancel($id)) {
            throw new BusinessException(ErrorCode::NOT_SUPPORT);
        }
        return true;
    }

    public function auditIndex(array $params): array
    {
        /**
         * 暂时不进行数据范围控制.(todo::使用数据范围控制).
         */
        //        $ids = $this->dao->getUpAuditObjIds();
        //        Arr::set($params, 'ids', $ids);
        $userDao = di()->get(UserDao::class);
        $orgIds = $userDao->getOrganizations();
        if (! empty($orgIds)) {
            Arr::set($params, 'audit_organization_id', $orgIds[0]);
        }
        return $this->getPageList($params);
    }

    public function index(array $params): array
    {
        return $this->getPageList($params);
    }

    public function publicIndex(array $params): array
    {
        //        Arr::set($params, 'audit_status', AuditCode::PASS->value);
        return $this->getPageList($params);
    }
}
