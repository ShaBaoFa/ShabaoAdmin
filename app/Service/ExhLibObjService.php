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
use App\Exception\BusinessException;
use Hyperf\Collection\Arr;

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

    public function info(int $id): array
    {
    }

    public function index()
    {

    }

    public function save(array $data): mixed
    {
        // 判断子分区的 lib_area_type
        if (Arr::get($data, 'lib_type') != di()->get(ExhLibAreaService::class)->value(['id' => Arr::get($data, 'lib_area_type')], 'lib_type')) {
            throw new BusinessException(ErrorCode::INVALID_PARAMS);
        }
        // 判断是否已存在
        $id = $this->value(['title' => Arr::get($data, 'title'), 'created_by' => user()->getId()]);
        if (! empty($id)) {
            return $id;
        }

        // 2 下架状态
        Arr::set($data, 'status', BaseCode::BASE_ABNORMAL);
        // 1 等待审核
        Arr::set($data, 'audit_status', AuditCode::IN_AUDIT);
        // 获取 hash 数组 对应的 id 数组
        $hashes = Arr::get($data, 'files');
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
}
