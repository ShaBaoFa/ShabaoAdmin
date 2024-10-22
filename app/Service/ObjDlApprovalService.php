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
use App\Dao\ExhLibObjDao;
use App\Dao\ObjDlApprovalDao;
use App\Dao\UserDao;
use App\Exception\BusinessException;
use App\Model\ExhLibObj;
use App\Model\UploadFile;
use Hyperf\Collection\Arr;

use function App\Helper\user;

class ObjDlApprovalService extends BaseService
{
    /**
     * @var ObjDlApprovalDao
     */
    public $dao;

    public function __construct(ObjDlApprovalDao $dao)
    {
        $this->dao = $dao;
    }

    public function save(array $data): mixed
    {
        $data = $this->handleData($data);
        return parent::save($data);
    }

    public function handleData($data): array
    {
        $userDao = di()->get(UserDao::class);
        $auditOrganizationId = $userDao->getParentOrganization();
        Arr::set($data, 'audit_organization_id', $auditOrganizationId);
        if ($auditOrganizationId == 0) {
            Arr::set($data, 'audit_status', AuditCode::PASS->value);
        }
        Arr::set($data, 'user_id', user()->getId());
        $objId = Arr::get($data, 'exh_lib_obj_id');
        $exhLibObjDao = di()->get(ExhLibObjDao::class);
        $obj = $exhLibObjDao->find($objId);
        /**
         * 1虚拟展项素材 2实体展项素材 3平台展项素材.
         * @var ExhLibObj $obj
         */
        Arr::set($data, 'type', $obj->type);
        Arr::set($data, 'exh_lib_obj_name', $obj->title);
        $cover = $obj->covers()->first();
        /**
         * @var UploadFile $cover
         */
        Arr::set($data, 'cover', $cover->hash);
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

    public function cancelDownloadApproval(int $id): bool
    {
        if (! $this->dao->cancelDownloadApproval($id)) {
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
        return $this->getPageList($params, false);
    }

    public function myDownloadApproval(array $params): array
    {
        Arr::set($params, 'created_by', user()->getId());
        return $this->getPageList($params, false);
    }
}
