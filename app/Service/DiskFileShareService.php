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

use App\Base\BaseModel;
use App\Base\BaseService;
use App\Constants\DiskFileCode;
use App\Constants\DiskFileShareExpireCode;
use App\Constants\ErrorCode;
use App\Dao\DiskShareDao;
use App\Exception\BusinessException;
use App\Model\DiskFile;
use App\Model\DiskFileShare;
use Hyperf\Collection\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

use function App\Helper\redis;
use function App\Helper\user;
use function Hyperf\Config\config;
use function Hyperf\Support\make;

class DiskFileShareService extends BaseService
{
    /**
     * @var DiskShareDao
     */
    public $dao;

    public function __construct(DiskShareDao $dao)
    {
        $this->dao = $dao;
    }

    public function save(array $data): array
    {
        $ids = Arr::get($data, 'items');
        foreach ($ids as $id) {
            $diskService = make(DiskService::class);
            if (! $diskService->belongMe(['id' => $id])) {
                throw new BusinessException(ErrorCode::DISK_FILE_NOT_EXIST);
            }
        }
        if (Arr::get($data, 'shared_with') && in_array(user()->getId(), Arr::get($data, 'shared_with'))) {
            throw new BusinessException(ErrorCode::DISK_CANNOT_SHARE_TO_YOURSELF);
        }
        Arr::set($data, 'share_link', $this->generateUniqueShareLink());
        $expire_at = DiskFileShareExpireCode::from((int) Arr::get($data, 'expire_type'))->getTimestamp();
        if ($expire_at) {
            Arr::set($data, 'expire_at', $expire_at);
        }
        $share = parent::save($data);
        if ($share) {
            /**
             * @todo 增加ws通知
             */
        }
        return $share;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getShareByLink(array $data): array
    {
        $share = $this->getShare($data);
        $key = sprintf('%sshare_link:uid_%s:%s', config('cache.default.prefix'), $share->created_by, Arr::get($data, 'share_link'));
        redis()->hIncrBy($key, DiskFileShare::getViewCountName(), 1);
        $this->numberOperation($share->id, DiskFileShare::getViewCountName());
        $pid = Arr::get($data, 'parent_id', 0);
        /**
         * @var DiskFileShare $share
         */
        $items = $this->getShareItems($share->id, (int) $pid);
        $shareArray = $share->toArray();
        Arr::set($shareArray, 'items', $items);
        /**
         * @todo 异步队列增加浏览量
         */
        return $shareArray;
    }

    public function getShare(array $data): null|BaseModel|DiskFileShare
    {
        $condition = [
            'share_password' => Arr::get($data, 'share_password'),
            'share_link' => Arr::get($data, 'share_link'),
        ];
        if (! $this->checkExists($condition)) {
            throw new BusinessException(ErrorCode::FORBIDDEN);
        }
        $share = $this->dao->first($condition, ['id', 'name', 'expire_at', 'permission', 'share_link']);
        /**
         * @var DiskFileShare $share
         */
        if (! $share->expire_at && $share->expire_at < time()) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        $shareWith = $share->shareWith()->select('id')->get()->pluck('id')->toArray();
        if ($shareWith && ! in_array(user()?->getId(), $shareWith)) {
            throw new BusinessException(ErrorCode::FORBIDDEN);
        }
        return $share;
    }

    /**
     * @param mixed $share
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAllShareItemsByType($share, DiskFileCode $type): array
    {
        $select = ['id', 'name', 'hash', 'level', 'type', 'parent_id'];
        $diskFiles = $share->diskFiles()->get($select);
        $allData = [];
        foreach ($diskFiles as $diskFile) {
            /**
             * @var DiskFile $diskFile
             */
            if ($type == DiskFileCode::TYPE_FILE && $diskFile->type == DiskFileCode::TYPE_FILE->value) {
                $allData[] = $diskFile->toArray();
                continue;
            }
            if ($type == DiskFileCode::TYPE_FOLDER && $diskFile->type == DiskFileCode::TYPE_FOLDER->value) {
                $allData[] = $diskFile->toArray();
            }
            $ds = di()->get(DiskService::class);
            $params = ['type' => $type->value];
            $descendants = $ds->getDescendants(parentId: $diskFile->id, params: $params, isScope: false, columns: $select);
            $allData = Arr::merge($allData, $descendants);
        }
        return $allData;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getShareDownloadToken(array $data): array
    {
        $share = $this->getShare($data);
        $key = sprintf('%sshare_link:uid_%s:%s', config('cache.default.prefix'), $share->created_by, Arr::get($data, 'share_link'));
        redis()->hIncrBy($key, DiskFileShare::getDownloadCountName(), 1);
        $this->numberOperation($share->id, DiskFileShare::getDownloadCountName());
        $items = $this->getAllShareItemsByType($share, DiskFileCode::TYPE_FILE);
        $hashes = [];
        foreach ($items as $item) {
            $hashes[] = Arr::get($item, 'hash');
        }
        // $hashes 中 是否包含数组里的所有值
        if (array_diff(Arr::get($data, 'hashes'), $hashes)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }
        $fs = di()->get(FileSystemService::class);
        return $fs->getDownloaderStsToken(Arr::get($data, 'hashes'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHash(int $folder_id, array $data): array
    {
        $share = $this->getShare($data);
        $allFolder = $this->getAllShareItemsByType($share, DiskFileCode::TYPE_FOLDER);
        $ids = [];
        foreach ($allFolder as $folder) {
            $ids[] = Arr::get($folder, 'id');
        }
        if (! in_array($folder_id, $ids)) {
            throw new BusinessException(ErrorCode::NOT_FOUND);
        }

        $cols = ['id', 'name', 'hash'];
        $ds = di()->get(DiskService::class);
        return $ds->getDescendants(parentId: $folder_id, params: ['type' => DiskFileCode::TYPE_FILE->value], isScope: false, columns: $cols);
    }

    protected function generateUniqueShareLink($length = 16): string
    {
        do {
            $shareLink = bin2hex(random_bytes($length / 2));
        } while ($this->dao->checkExists(['share_link' => $shareLink], false));
        return $shareLink;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getShareItems(int $shareId, int $pid = 0): array
    {
        return $this->dao->getShareItems($shareId, $pid);
    }
}
