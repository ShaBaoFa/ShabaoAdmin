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

namespace App\Base;

use App\Constants\ErrorCode;
use App\Constants\FileSystemCode;
use App\Constants\UploadStatusCode;
use App\Events\AfterUpload;
use App\Exception\BusinessException;
use Exception;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Stringable\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use RedisException;

use function App\Helper\format_size;
use function Hyperf\Support\env;

class BaseUpload
{
    protected FilesystemFactory $factory;

    protected Filesystem $filesystem;

    protected BaseRequest $request;

    protected EventDispatcherInterface $eventDispatcher;

    protected IdGeneratorInterface $idGenerator;

    protected ConfigInterface $config;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function __construct(
        FilesystemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        BaseRequest $request,
        IdGeneratorInterface $idGenerator,
        ConfigInterface $config
    ) {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $request;
        $this->filesystem = $factory->get($this->getMappingMode());
        $this->idGenerator = $idGenerator;
        $this->config = $config;
    }

    public function getFileSystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * 上传文件.
     * @throws FileExistsException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws FilesystemException
     */
    public function upload(UploadedFile $uploadedFile, array $config = []): array
    {
        var_dump('上传文件');
        return $this->handleUpload($uploadedFile, $config);
    }

    /**
     * 组装url.
     */
    public function assembleUrl(?string $path, string $filename): string
    {
        return $this->getPath($path, true) . '/' . $filename;
    }

    /**
     * 获取编码后的文件名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getNewName(): string
    {
        return (string) $this->idGenerator->generate();
    }

    /**
     * 获取存储方式.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getStorageMode(): int|string
    {
        return FileSystemCode::LOCAL->value;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     */
    public function handlePreparation(array $metadata, array $config): array
    {
        $segments = explode('.', $metadata['origin_name']);
        $suffix = Str::lower((string) end($segments));
        $path = $this->getPath($config['path'] ?? null, $this->getStorageMode() != FileSystemCode::LOCAL->value);
        $filename = $this->getNewName() . '.' . $suffix;
        return [
            'storage_mode' => $this->getStorageMode(),
            'origin_name' => $metadata['origin_name'],
            'object_name' => $filename,
            'mime_type' => $metadata['mime_type'],
            'storage_path' => $path,
            'status' => UploadStatusCode::UPLOAD_UNFINISHED->value,
            'hash' => $config['hash'],
            'suffix' => $suffix,
            'size_byte' => (int) $metadata['size_byte'],
            'size_info' => format_size((int) $metadata['size_byte'] * 1024),
            'url' => $this->assembleUrl($config['path'] ?? null, $filename),
        ];
    }

    /**
     * 处理上传.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws FilesystemException
     */
    protected function handleUpload(UploadedFile $uploadedFile, array $config): array
    {
        $tmpFile = $uploadedFile->getPath() . '/' . $uploadedFile->getFilename();
        var_dump($this->getStorageMode());
        var_dump(FileSystemCode::LOCAL->value);
        $path = $this->getPath($config['path'] ?? null, $this->getStorageMode() != FileSystemCode::LOCAL->value);
        $filename = $this->getNewName() . '.' . Str::lower($uploadedFile->getExtension());
        var_dump($path . '/' . $filename);
        try {
            $this->filesystem->writeStream($path . '/' . $filename, $uploadedFile->getStream()->detach());
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $e->getMessage());
        }

        $fileInfo = [
            'storage_mode' => $this->getStorageMode(),
            'origin_name' => $uploadedFile->getClientFilename(),
            'object_name' => $filename,
            'mime_type' => $uploadedFile->getClientMediaType(),
            'storage_path' => $path,
            'hash' => md5_file($tmpFile),
            'suffix' => Str::lower($uploadedFile->getExtension()),
            'size_byte' => $uploadedFile->getSize(),
            'size_info' => format_size($uploadedFile->getSize() * 1024),
            'url' => $this->assembleUrl($config['path'] ?? null, $filename),
        ];

        $this->eventDispatcher->dispatch(new AfterUpload($fileInfo));

        return $fileInfo;
    }

    /**
     * @param false $isContainRoot
     */
    protected function getPath(?string $path = null, bool $isContainRoot = false): string
    {
        $uploadfile = $isContainRoot ? '/' . env('UPLOAD_PATH', 'uploadfile') . '/' : '';
        return empty($path) ? $uploadfile . date('Ymd') : $uploadfile . $path;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    protected function getMappingMode(): string
    {
        return Str::lower(FileSystemCode::tryFrom($this->getStorageMode())->name ?? FileSystemCode::LOCAL->name);
    }
}
