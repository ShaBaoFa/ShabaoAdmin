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
use App\Constants\DiskFileCode;
use App\Constants\ErrorCode;
use App\Dao\DiskDao;
use App\Exception\BusinessException;
use App\Model\DiskFile;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

use function Hyperf\Stringable\str;

class DiskService extends BaseService
{
    /**
     * @var DiskDao
     */
    public $dao;

    public function __construct(DiskDao $dao)
    {
        $this->dao = $dao;
    }

    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getTreeList($params, $isScope);
    }

    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getTreeListByRecycle($params, $isScope);
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        return $this->dao->getSelectTree();
    }

    /**
     * 更新.
     */
    public function update(mixed $id, array $data): bool
    {
        $handleData = $this->handleData($data);
        if (! $this->checkChildrenExists($id)) {
            return $this->dao->update($id, $handleData);
        }
        $update[] = [
            'id' => $id,
            'data' => $handleData,
        ];
        $descendants = $this->dao->getDescendants((int) $id);
        foreach ($descendants as $descendant) {
            $handleDescendantLevelData = $this->handleDescendantLevels($descendant['level'], $handleData['level'], $id);
            $update[] = [
                'id' => $descendant['id'],
                'data' => ['level' => $handleDescendantLevelData],
            ];
        }
        return $this->dao->batchUpdate($update);
    }

    /**
     * 真实删除部门.
     */
    public function realDel(array $ids): ?array
    {
        // 跳过的部门
        $ctuIds = [];
        if (count($ids)) {
            foreach ($ids as $id) {
                if (! $this->checkChildrenExists((int) $id) && ! $this->dao->find($id)->users()->exists()) {
                    $this->dao->realDelete([$id]);
                } else {
                    $ctuIds[] = $id;
                }
            }
        }
        return count($ctuIds) ? $this->dao->getDeptName($ctuIds) : null;
    }

    public function saveFiles(array $filesData): bool
    {
        foreach ($filesData as $fileData) {
            $this->dao->save($this->handleFileData($fileData));
        }
        return true;
    }

    public function saveFolder(array $data): int
    {
        return $this->dao->save($this->handleFolderData($data));
    }

    public function getDownloadTokens(array $hashes): array
    {
        if (! $this->belongMe($hashes)) {
            throw new BusinessException(ErrorCode::DISK_FILE_NOT_EXIST);
        }
        $fs = di()->get(FileSystemService::class);
        return $fs->getDownloaderStsToken($hashes);
    }

    public function getFolderMeta(int $folder_id = 0): array
    {
        if ($folder_id > 0) {
            (! $this->dao->isFolder($folder_id)) && throw new BusinessException(ErrorCode::DISK_FOLDER_NOT_EXIST);
        }
        $currentFolder = $this->find($folder_id, ['id', 'name', 'level', 'parent_id', 'type', 'size_byte', 'size_info']);
        /**
         * @var DiskFile $currentFolder
         */
        $folders = explode(',', $currentFolder->level);
        $ancestor = [];
        foreach ($folders as $key => $folderId) {
            if ((int) $folderId == 0) {
                Arr::set($ancestor, $folderId, '根目录');
                continue;
            }
            $folder = $this->find($folderId);
            /**
             * @var DiskFile $folder
             */
            Arr::set($ancestor, $folderId, $folder->getName());
        }
        return array_merge($currentFolder->toArray(), [
            'ancestor' => $ancestor,
        ]);
    }

    public function listContents(int $folder_id = 0): array
    {
        if ($folder_id > 0) {
            (! $this->dao->isFolder($folder_id)) && throw new BusinessException(ErrorCode::DISK_FOLDER_NOT_EXIST);
        }
        return $this->getList([
            'parent_id' => $folder_id,
        ]);
    }

    public function renameItem(int $item_id, string $newName): bool
    {
        /**
         * @var DiskFile $item
         */
        $item = $this->dao->find($item_id);
        // 1. 检查文件是否存在
        if (! $item) {
            throw new BusinessException(ErrorCode::DISK_FILE_NOT_EXIST);
        }
        if ($item->name == $newName) {
            return true;
        }
        $item = $item->toArray();
        // 2. 检查文件名是否已存在
        Arr::set($item, 'name', $newName);
        while ($this->dao->checkNameExists((int) Arr::get($item, 'parent_id', 0), Arr::get($item, 'name'), $item_id)) {
            if (Arr::get($item, 'type') == DiskFileCode::TYPE_FOLDER->value) {
                $item = $this->getNewFolderName($item);
                continue;
            }
            $item = $this->getNewFileName($item);
        }
        return $this->dao->update($item_id, $item);
    }

    private function getNewFolderName(array $data): array
    {
        Arr::set($data, 'name', str(Arr::get($data, 'name')) . '_' . Str::random(6));
        return $data;
    }

    /**
     * 处理文件数据.
     */
    private function handleFileData(array $data): array
    {
        // 文件name、hash
        $fs = di()->get(FileSystemService::class);
        if (! $fs->dao->isUploaded(Arr::get($data, 'hash'))) {
            throw new BusinessException(ErrorCode::FILE_HAS_NOT_BEEN_UPLOADED);
        }
        $file = $fs->dao->getFileInfoByHash(Arr::get($data, 'hash'));
        // 文件数据
        Arr::set($data, 'suffix', Arr::get($file, 'suffix'));
        Arr::set($data, 'size_byte', Arr::get($file, 'size_byte'));
        Arr::set($data, 'size_info', Arr::get($file, 'size_info'));
        // 类型
        Arr::set($data, 'type', DiskFileCode::TYPE_FILE);
        // 文件类型
        Arr::set($data, 'file_type', $this->getFileTypeBySuffix($data)->value);
        return $this->handleData($data);
    }

    /**
     * 处理数据.
     */
    private function handleData(array $data): array
    {
        $pid = (int) Arr::get($data, 'parent_id', 0);
        if ($pid > 0) {
            (! $this->dao->isFolder($pid)) && throw new BusinessException(ErrorCode::DISK_FOLDER_NOT_EXIST);
        }

        while ($this->dao->checkNameExists($pid, Arr::get($data, 'name'), Arr::get($data, $this->dao->getModel()->getKeyName()))) {
            if (Arr::get($data, 'type') == DiskFileCode::TYPE_FOLDER->value) {
                $data = $this->getNewFolderName($data);
                continue;
            }
            $data = $this->getNewFileName($data);
        }
        // 文件level
        return $this->handleLevel($data);
    }

    private function handleLevel(array $data): array
    {
        if (Arr::get($data, 'parent_id', 0) === 0) {
            Arr::set($data, 'level', (string) Arr::get($data, 'parent_id', '0'));
        } else {
            $parent = $this->find((int) Arr::get($data, 'parent_id'));
            /**
             * @var DiskFile $parent
             */
            Arr::set($data, 'level', $parent->level . ',' . Arr::get($data, 'parent_id'));
        }
        return $data;
    }

    private function getFileTypeBySuffix($data): DiskFileCode
    {
        return match (Arr::get($data, 'suffix')) {
            // FILE_TYPE_IMAGE
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp' => DiskFileCode::FILE_TYPE_IMAGE,
            // FILE_TYPE_VIDEO
            'mp4', 'avi', 'mkv', 'mov', 'flv', 'webm' => DiskFileCode::FILE_TYPE_VIDEO,
            // FILE_TYPE_AUDIO
            'mp3', 'wav', 'wma', 'm4a' => DiskFileCode::FILE_TYPE_AUDIO,
            // FILE_TYPE_DOCUMENT
            'pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx', 'xls', 'xlsx' => DiskFileCode::FILE_TYPE_DOCUMENT,
            // FILE_TYPE_OTHER
            default => DiskFileCode::FILE_TYPE_OTHER,
        };
    }

    private function handleFolderData($data): array
    {
        Arr::set($data, 'type', DiskFileCode::TYPE_FOLDER->value);
        return $this->handleData($data);
    }

    private function belongMe(array $hashes): bool
    {
        foreach ($hashes as $hash) {
            if (! $this->dao->belongMe($hash)) {
                return false;
            }
        }
        return true;
    }

    private function getNewFileName(array $data): array
    {
        // 先根据'.'获取到文件名部分
        $name = explode('.', Arr::get($data, 'name'));
        $name = Arr::get($name, 0) . '_' . Str::random(6);
        // 添加随机字符之后再拼接回去
        Arr::set($data, 'name', $name . '.' . Arr::get($data, 'suffix'));
        return $data;
    }
}
