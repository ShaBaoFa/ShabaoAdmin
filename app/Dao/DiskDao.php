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

use App\Base\BaseCollection;
use App\Base\BaseDao;
use App\Constants\DiskFileCode;
use App\Model\DiskFile;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Annotation\Transactional;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
            fn (Builder $query) => $query->where(
                function (Builder $query) use ($level) {
                    if ($level !== 0) {
                        $query->where('level', 'like', '%,' . $level)
                            ->orWhere('level', 'like', '%,' . $level . ',%');
                    }
                }
            )
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

        $query->when(
            Arr::get($params, 'list_recycle'),
            function (Builder $query) {
                $query->where('is_deleted', '=', true)
                    ->where(function (Builder $query) {
                        $query->where('parent_id', '=', 0) // 顶级目录
                            ->orWhereDoesntHave('parent', function (Builder $q) {
                                $q->withoutGlobalScopes()->where('is_deleted', '=', true); // 禁用软删除作用域，避免 deleted_at 条件
                            }); // 父目录没有被软删除`
                    });
            }
        );

        return $query;
    }

    public function isFolder(int $folderId): bool
    {
        return $this->model::query()->find($folderId)?->type == DiskFileCode::TYPE_FOLDER->value;
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
    public function checkNameExists(int $parentId, string $name, ?int $id = null): bool
    {
        $query = $this->model::query();
        if (! is_null($id)) {
            $query->where($this->getModel()->getKeyName(), '<>', $id);
        }
        $query->where('parent_id', $parentId)->where('name', $name)->userDataScope();
        return $query->exists();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Transactional]
    public function delete($ids): bool
    {
        $deleteIds = [];
        foreach ($ids as $id) {
            $file = $this->model::find($id);
            if (is_null($file)) {
                continue;
            }
            $this->update($file->id, ['is_deleted' => true]);
            // 如果是文件夹，需要递归删除所有子文件和子文件夹
            if ($file->type == DiskFileCode::TYPE_FOLDER->value) {
                $deleteIds = Arr::merge($deleteIds, $this->getDescendants(parentId: $file->id, columns: ['id']));
            }
        }
        return parent::delete(Arr::merge($ids, $deleteIds));
    }

    #[Transactional]
    public function realDelete($ids): bool
    {
        $deleteIds = [];
        foreach ($ids as $key => $id) {
            $file = $this->model::onlyTrashed()->find($id);
            if (is_null($file)) {
                Arr::forget($ids, $key);
                continue;
            }
            // 如果是文件夹，需要递归删除所有子文件和子文件夹
            if ($file->type == DiskFileCode::TYPE_FOLDER->value) {
                $deleteIds = Arr::merge($deleteIds, $this->getDescendants(parentId: $file->id, params: ['recycle' => true], columns: ['id']));
            }
        }
        return parent::realDelete(Arr::merge($ids, $deleteIds));
    }

    #[Transactional]
    public function recovery(array $ids): bool
    {
        $recoveryIds = [];
        foreach ($ids as $id) {
            $file = $this->model::onlyTrashed()->find($id);
            if (is_null($file)) {
                continue;
            }
            // 如果是文件夹，需要递归删除所有子文件和子文件夹
            if ($file->type == DiskFileCode::TYPE_FOLDER->value) {
                $recoveryIds = Arr::merge($recoveryIds, $this->getDescendants(parentId: $file->id, params: ['recycle' => true], columns: ['id']));
            }
        }
        parent::recovery(Arr::merge($ids, $recoveryIds));
        foreach ($ids as $id) {
            $this->update($id, ['is_deleted' => false]);
        }
        return true;
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        $treeData = $this->model::query()->where('type', DiskFileCode::TYPE_FOLDER->value)->select(['id', 'parent_id', 'id AS value', 'name AS label'])
            ->orderBy('parent_id')
            ->userDataScope()
            ->get()->toArray();

        return (new BaseCollection())->toTree($treeData, $treeData[0]['parent_id'] ?? 0);
    }
}
