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

use App\Base\BaseCollection;
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
        $key = sprintf('%sshare_link:%s', config('cache.default.prefix'), Arr::get($data, 'share_link'));
        redis()->incr($key);
        $this->numberOperation($share->id, DiskFileShare::getViewCountName());
        $tree = $this->getDiskFilesTree($share);
        $shareArray = $share->toArray();
        Arr::set($shareArray, 'items', $tree);
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

    public function getDiskFilesTree($share): array
    {
        $select = ['id', 'name', 'hash', 'level', 'type', 'parent_id'];
        $diskFiles = $share->diskFiles()->get($select);
        $treeData = [];
        foreach ($diskFiles as $diskFile) {
            /**
             * @var DiskFile $diskFile
             */
            $treeData[] = $diskFile->toArray();
            if ($diskFile->type == DiskFileCode::TYPE_FILE->value) {
                continue;
            }
            $ds = di()->get(DiskService::class);
            $descendants = $ds->getDescendants(parentId: $diskFile->id, isScope: false, columns: $select);
            $treeData = Arr::merge($treeData, $descendants);
        }
        return (new BaseCollection())->toTree($treeData);
    }

    protected function generateUniqueShareLink($length = 16): string
    {
        do {
            $shareLink = bin2hex(random_bytes($length / 2));
        } while ($this->dao->checkExists(['share_link' => $shareLink], false));
        return $shareLink;
    }
}
