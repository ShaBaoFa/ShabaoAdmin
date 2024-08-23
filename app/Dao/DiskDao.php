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
use App\Constants\DiskFileCode;
use App\Model\DiskFile;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;

use function App\Helper\user;

class DiskDao extends BaseDao
{
    /**
     * @var DiskFile
     */
    public $model;

    /**
     * 查询的菜单字段.
     */
    public array $diskFileField = ['id', 'parent_id', 'level', 'name', 'leader', 'phone', 'status', 'sort', 'created_by', 'updated_by', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    public function assignModel(): void
    {
        $this->model = DiskFile::class;
    }

    /**
     * 批量更新.
     */
    #[Transactional]
    public function batchUpdate(array $update): bool
    {
        foreach ($update as $item) {
            $result = parent::update($item['id'], $item['data']);
            if (! $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 批量保存.
     */
    #[Transactional]
    public function batchSave(array $save): bool
    {
        foreach ($save as $item) {
            $result = parent::save($item);
            if (! $result) {
                return false;
            }
        }
        return true;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            $level = Arr::get($params, 'level'),
            fn (Builder $query) => $query->where('level', 'like', '%' . $level . '%')
        );

        $query->when(
            $name = Arr::get($params, 'name'),
            fn (Builder $query) => $query->where('name', 'like', '%' . $name . '%')
        );

        $query->when(
            Arr::accessible($hashes = Arr::get($params, 'hashes')),
            fn (Builder $query) => $query->whereIn('hash', $hashes)
        );

        $query->when(
            ! is_null($parent_id = Arr::get($params, 'parent_id')),
            fn (Builder $query) => $query->where('parent_id', $parent_id)
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

    public function isFolder(int $folderId): bool
    {
        return $this->model::query()->find($folderId)->value('type') == DiskFileCode::TYPE_FOLDER->value;
    }

    public function areFolders(array $folderIds): bool
    {
        foreach ($folderIds as $folderId) {
            if (! $this->isFolder($folderId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查重名情况.
     */
    public function checkNameExists(int $parentId, string $name): bool
    {
        return $this->checkExists(['parent_id' => $parentId, 'name' => $name, $this->getModel()->getDataScopeField() => user()->getId()]);
    }

    /**
     * 文件归属.
     */
    public function belongMe(string $hash): bool
    {
        return $this->checkExists(Arr::merge(['hash' => $hash], [$this->getModel()->getDataScopeField() => user()->getId()]));
    }
}
