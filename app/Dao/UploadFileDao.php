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
use App\Constants\FileSystemCode;
use App\Constants\UploadStatusCode;
use App\Events\RealDeleteUploadFile;
use App\Model\UploadFile;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class UploadFileDao extends BaseDao
{
    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;

    #[Inject]
    protected ContainerInterface $container;

    public function assignModel(): void
    {
        $this->model = UploadFile::class;
    }

    /**
     * 通过hash获取上传文件的信息.
     */
    public function getFileInfoByHash(string $hash, array $columns = ['*']): ?array
    {
        $model = $this->model::query()->where('hash', $hash)->first($columns);
        if (! $model) {
            $model = $this->model::withTrashed()->where('hash', $hash)->first(['id']);
            $model && $model->forceDelete();
            return null;
        }

        return $model->toArray();
    }

    public function isUploaded(string $hash): bool
    {
        return $this->model::query()->where('hash', $hash)->where('status', UploadStatusCode::UPLOAD_FINISHED->value)->exists();
    }

    public function changeStatusByHash(string $hash): bool
    {
        return $this->updateByCondition(['hash' => $hash], ['status' => UploadStatusCode::UPLOAD_FINISHED->value]);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        $query->when(
            Arr::get($params, 'storage_mode'),
            fn (Builder $query, $storageMode) => $query->where('storage_mode', $storageMode)
        );

        $query->when(
            Arr::get($params, 'origin_name'),
            fn (Builder $query, $originName) => $query->where('origin_name', 'like', '%' . $originName . '%')
        );

        $query->when(
            Arr::get($params, 'storage_path'),
            fn (Builder $query, $storagePath) => $query->where('storage_path', 'like', $storagePath . '%')
        );

        $query->when(
            Arr::get($params, 'mime_type'),
            fn (Builder $query, $mimeType) => $query->where('mime_type', 'like', $mimeType . '/%')
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                /**
                 * @var UploadFile $model
                 */
                $storageMode = Str::lower(FileSystemCode::tryFrom($model->storage_mode)->name ?? FileSystemCode::LOCAL->name);
                $event = new RealDeleteUploadFile(
                    $model,
                    $this->container->get(FilesystemFactory::class)->get($storageMode)
                );
                $this->eventDispatcher->dispatch($event);
                if ($event->getConfirm()) {
                    $model->forceDelete();
                }
            }
        }
        unset($event);
        return true;
    }

    /**
     * 检查目录是否存在.
     */
    public function checkDirDbExists(string $path): bool
    {
        return $this->model::withTrashed()
            ->where('storage_path', $path)
            ->orWhere('storage_path', 'like', $path . '/%')
            ->exists();
    }
}
