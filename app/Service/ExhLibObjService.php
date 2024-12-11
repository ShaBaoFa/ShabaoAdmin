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
use App\Constants\BaseCode;
use App\Constants\ErrorCode;
use App\Dao\ExhLibObjDao;
use App\Dao\UploadFileDao;
use App\Dao\UserDao;
use App\Events\AuditMessageSent;
use App\Exception\BusinessException;
use App\Model\ExhLibObj;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Collection\Arr;

use function App\Helper\ev_dispatch;
use function App\Helper\user;

class ExhLibObjService extends BaseService
{
    /**
     * @var ExhLibObjDao
     */
    public $dao;

    /**
     * @param ExhLibObjDao $dao
     */
    public UploadFileDao $uploadFileDao;

    public function __construct(ExhLibObjDao $dao, UploadFileDao $uploadFileDao)
    {
        $this->dao = $dao;
        $this->uploadFileDao = $uploadFileDao;
    }

    public function getPublicIndex(array $params): array
    {
        Arr::set($params, 'select', 'id,title,author,star_count,collect_count,profile,created_at');
        Arr::set($params, 'audit_status', AuditCode::PASS->value);
        Arr::set($params, 'status', BaseCode::BASE_NORMAL->value);
        Arr::set($params, '_with', ['covers']);
        return parent::getPageList($params, false);
    }

    public function index(array $params): array
    {
        Arr::set($params, 'select', 'id,title,author,status,audit_status,star_count,collect_count');
        Arr::set($params, '_with', ['covers']);
        return parent::getPageList($params);
    }

    public function recycle(array $params): array
    {
        Arr::set($params, 'select', 'id,title,author,audit_status');
        Arr::set($params, '_with', ['covers']);
        return parent::getPageListByRecycle($params);
    }

    public function info($id): array
    {
        if (! $this->checkExists(['id' => $id], false)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        $modelArray = $this->getCacheData($id);
        Arr::set($modelArray, 'hasStared', $this->dao->hasStared($id, user()->getId()));
        Arr::set($modelArray, 'hasCollected', $this->dao->hasCollected($id, user()->getId()));
        Arr::set($modelArray, 'hasPicked', $this->dao->hasPicked($id, user()->getId()));
        return $modelArray;
    }

    #[CacheEvict(prefix: 'ExhLibOjb', value: 'ExhLibObjId_#{id}')]
    public function update(mixed $id, array $data): bool
    {
        // 判断是否已存在
        if ($this->checkExists(condition: [
            'lib_type' => Arr::get($data, 'lib_type'),
            'title' => Arr::get($data, 'title'),
        ], id: $id)) {
            throw new BusinessException(ErrorCode::INVALID_PARAMS);
        }
        // 3个区+保存位置 都无法修改
        $model = $this->find($id);
        if (! $model) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        /**
         * @var ExhLibObj $model
         */
        Arr::set($data, 'type', $model->type);
        Arr::set($data, 'lib_type', $model->lib_type);
        Arr::set($data, 'lib_area_type', $model->lib_area_type);
        Arr::set($data, 'save_dir_id', $model->save_dir_id);
        // 2 下架状态
        Arr::set($data, 'status', BaseCode::BASE_ABNORMAL->value);
        // 1 等待审核
        Arr::set($data, 'audit_status', AuditCode::IN_AUDIT->value);
        // 获取 审批组织
        $userDao = di()->get(UserDao::class);
        $orgId = $userDao->getParentOrganization();
        Arr::set($data, 'audit_organization_id', $orgId);
        if ($orgId == 0) {
            Arr::set($data, 'audit_status', AuditCode::PASS->value);
        }
        // 获取 hash 数组 对应的 id 数组
        $hashes = Arr::get($data, 'files');
        $newIds = $this->uploadFileDao->getIdsByHashes($hashes);
        $oldIds = $model->files()->pluck('id')->toArray();
        // 筛选新增的
        $addIds = array_diff($newIds, $oldIds);
        $saveFiles = [];
        $diskService = di()->get(DiskService::class);
        $fs = di()->get(FileSystemService::class);
        $hashes = $fs->findMany($addIds)->pluck('hash')->toArray();
        foreach ($hashes as $hash) {
            $saveFiles[] = [
                'hash' => $hash,
                'parent_id' => Arr::get($data, 'save_dir_id'),
            ];
        }
        $diskService->saveFiles($saveFiles);
        Arr::set($data, 'files', $newIds);
        // 获取 封面 数组
        $covers = Arr::get($data, 'covers');
        $coverIds = $this->uploadFileDao->getIdsByHashes($covers);
        Arr::set($data, 'covers', $coverIds);
        return parent::update($id, $data);
    }

    public function save(array $data): mixed
    {
        // 获取 审批组织
        $userDao = di()->get(UserDao::class);
        $orgId = $userDao->getParentOrganization();
        Arr::set($data, 'audit_organization_id', $orgId);
        if ($orgId == 0) {
            Arr::set($data, 'audit_status', AuditCode::PASS->value);
        }
        // 判断子分区的 lib_area_type
        if (Arr::get($data, 'lib_type') != di()->get(ExhLibAreaService::class)->value(['id' => Arr::get($data, 'lib_area_type')], 'lib_type')) {
            throw new BusinessException(ErrorCode::INVALID_PARAMS);
        }
        // 判断是否已存在
        $id = $this->value([
            'lib_type' => Arr::get($data, 'lib_type'),
            'title' => Arr::get($data, 'title'),
            'created_by' => user()->getId(),
        ]);
        if (! empty($id)) {
            return $id;
        }

        // 2 下架状态
        Arr::set($data, 'status', BaseCode::BASE_ABNORMAL);
        // 1 等待审核
        Arr::set($data, 'audit_status', AuditCode::IN_AUDIT);
        // 获取 hash 数组 对应的 id 数组
        $hashes = Arr::get($data, 'files', []);
        $saveFiles = [];
        $diskService = di()->get(DiskService::class);
        foreach ($hashes as $hash) {
            $saveFiles[] = [
                'hash' => $hash,
                'parent_id' => Arr::get($data, 'save_dir_id'),
            ];
        }
        $ids = $this->uploadFileDao->getIdsByHashes($hashes);
        // 获取 封面 数组
        $covers = Arr::get($data, 'covers');
        $coverIds = $this->uploadFileDao->getIdsByHashes($covers);
        $diskService->saveFiles($saveFiles);
        // 设置FILE ID数组
        Arr::set($data, 'files', $ids);
        // 设置 封面 ID数组
        Arr::set($data, 'covers', $coverIds);
        return $this->dao->save($data);
    }

    public function addStar(int $id): bool
    {
        ! $this->dao->addStar($id) && throw new BusinessException(ErrorCode::REPEAT_OPERATION);
        return true;
    }

    public function cancelStar(int $id): bool
    {
        ! $this->dao->cancelStar($id) && throw new BusinessException(ErrorCode::REPEAT_OPERATION);
        return true;
    }

    public function addCollection(int $id): bool
    {
        ! $this->dao->addCollection($id) && throw new BusinessException(ErrorCode::REPEAT_OPERATION);
        return true;
    }

    public function cancelCollection(int $id): bool
    {
        ! $this->dao->cancelCollection($id) && throw new BusinessException(ErrorCode::REPEAT_OPERATION);
        return true;
    }

    public function addPick(int $id): bool
    {
        ! $this->dao->addPick($id) && throw new BusinessException(ErrorCode::REPEAT_OPERATION);
        return true;
    }

    public function cancelPick(int $id): bool
    {
        ! $this->dao->cancelPick($id) && throw new BusinessException(ErrorCode::REPEAT_OPERATION);
        return true;
    }

    public function auditIndex(array $params)
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
        Arr::set($params, 'status', BaseCode::BASE_NORMAL->value);
        Arr::set($params, '_with', ['covers']);
        return $this->getPageList($params, false);
    }

    public function changeAuditStatus(int $id, int $auditStatus, string $refuse_reason = ''): bool
    {
        if (! $this->find($id)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        if (! $this->dao->changeAuditStatus($id, $auditStatus, $refuse_reason)) {
            throw new BusinessException(ErrorCode::NOT_SUPPORT);
        }
        // 发送审核通知
        $payload = [];
        Arr::set($payload, 'send_by', user()->getId());
        /**
         * @var ExhLibObj $exhLibObj
         */
        $exhLibObj = $this->find($id);
        Arr::set($payload, 'receive_by', [$exhLibObj->created_by]);
        Arr::set($payload, 'content', $exhLibObj->toArray());
        $event = new AuditMessageSent($payload);
        ev_dispatch()->dispatch($event);
        return true;
    }

    #[Cacheable(prefix: 'ExhLibOjb', value: 'ExhLibObjId_#{id}', ttl: 3600)]
    private function getCacheData($id): array
    {
        return $this->dao->find($id)->load(['tags', 'files', 'covers', 'share_regions'])->toArray();
    }
}
