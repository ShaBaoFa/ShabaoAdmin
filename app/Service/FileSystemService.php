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
use App\Base\BaseUpload;
use App\Constants\ErrorCode;
use App\Constants\FileSystemCode;
use App\Dao\UploadFileDao;
use App\Exception\BusinessException;
use Carbon\Carbon;
use Exception;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Stringable\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use Swoole\Coroutine\System;

class FileSystemService extends BaseService
{
    /**
     * @var UploadFileDao
     */
    public $dao;

    #[Inject]
    protected ConfigInterface $config;

    protected BaseUpload $uploadTool;

    public function __construct(UploadFileDao $dao, BaseUpload $uploadTool)
    {
        $this->dao = $dao;
        $this->uploadTool = $uploadTool;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     * @throws NotFoundExceptionInterface
     */
    public function upload(UploadedFile $uploadedFile, array $config = []): array
    {
        try {
            $hash = md5_file($uploadedFile->getPath() . '/' . $uploadedFile->getFilename());
            if ($data = $this->dao->getFileInfoByHash($hash)) {
                return $data;
            }
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::HASH_VERIFICATION_FAILED);
        }
        $data = $this->uploadTool->upload($uploadedFile, $config);
        if ($this->save($data)) {
            return $data;
        }
        return [];
    }

    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getPageList($params, $isScope);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getFileByHash(string $hash, array $columns = ['*'], bool $returnFs = false): array
    {
        $file = $this->dao->getFileInfoByHash($hash, $columns);
        if (empty($file)) {
            throw new BusinessException(ErrorCode::FILE_NOT_EXIST);
        }
        $storageMode = Str::lower(FileSystemCode::tryFrom($file['storage_mode'])->name ?? FileSystemCode::LOCAL->name);
        $filesystem = di()->get(FilesystemFactory::class)->get($storageMode);
        match ($file['storage_mode']) {
            FileSystemCode::OSS->value => $file['signature'] = $this->generateSignature($filesystem, $file),
            default => $file['signature'] = null
        };
        return $returnFs ? [$filesystem, $file] : $file;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     */
    public function responseFileByHash(string $hash): array
    {
        [$filesystem, $file] = $this->getFileByHash($hash, returnFs: true);
        if ($file['storage_mode'] != FileSystemCode::LOCAL->value && $file['size_byte'] > 4 * 1024 * 1024) {
            throw new BusinessException(ErrorCode::FILE_TOO_LARGE_TO_READ);
        }
        /**
         * @var Filesystem $filesystem
         */
        $context = $filesystem->read($file['url']);
        return [$file, $context];
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     */
    public function downloadFileByHash(string $hash): array
    {
        [$file, $context] = $this->responseFileByHash($hash);
        $tempPath = tempnam(sys_get_temp_dir(), 'tmp') . '.' . $file['suffix'];
        System::writeFile($tempPath, $context);
        return [$tempPath, $file];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getUploaderStsToken(array $metadata, array $config): array
    {
        try {
            $hash = md5(json_encode($metadata));
            if ($fileInfo = $this->dao->getFileInfoByHash($hash)) {
                return ['fileInfo' => $fileInfo,'sts' => null];
            }
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::HASH_VERIFICATION_FAILED);
        }
        if ($this->uploadTool->getStorageMode() != FileSystemCode::OSS->value) {
            throw new BusinessException(ErrorCode::STS_NOT_SUPPORT);
        }
        try {
            [$fileInfo, $credentials] = $this->uploadTool->handleStsUpload($metadata, $config);
            if ($this->save($fileInfo)) {
                return ['file_info' => $fileInfo,'sts' => $credentials];
            }
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::UPLOAD_FAILED);
        }
        return [];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    private function generateSignature(Filesystem $filesystem, array $data): string
    {
        return match ($data['storage_mode']) {
            FileSystemCode::OSS->value => $filesystem->temporaryUrl($data['url'], Carbon::now()->addHour()),
            default => $data['url']
        };
    }
}
