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
use App\Constants\UploadStatusCode;
use App\Dao\UploadFileDao;
use App\Exception\BusinessException;
use Carbon\Carbon;
use Exception;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Collection\Arr;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use Swoole\Coroutine\System;
use Wlfpanda1012\AliyunSts\Oss\OssRamService;

use function App\Helper\user;
use function Hyperf\Support\make;

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
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     */
    public function responseFileByHash(string $hash): array
    {
        $file = $this->getFileInfoByHash($hash);
        if ($file['storage_mode'] != FileSystemCode::LOCAL->value && $file['size_byte'] > 4 * 1024 * 1024) {
            throw new BusinessException(ErrorCode::FILE_TOO_LARGE_TO_READ);
        }
        /**
         * @var Filesystem $filesystem
         */
        $context = $this->uploadTool->getFileSystem()->read($file['url']);
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
    #[Cacheable(prefix: 'uploaderStsToken', value: 'fileHash_#{hash}', ttl: 900)]
    public function getUploaderStsToken(string $hash): array
    {
        if ($this->dao->isUploaded($hash)) {
            throw new BusinessException(ErrorCode::FILE_HAS_BEEN_UPLOADED);
        }
        $file = $this->getFileInfoByHash($hash);
        try {
            $sts = $this->config->get('sts');
            $ossRamService = make(OssRamService::class, ['option' => $sts]);
            $customParams = ['hash' => $hash];
            return ['callback_custom_params' => $customParams, 'credentials' => $ossRamService->allowPutObject($file['url'])];
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::GET_STS_TOKEN_FAIL);
        }
    }

    public function getDownloaderStsToken(array|string $hashes): array
    {
        if (! Arr::accessible($hashes)) {
            $hashes = [$hashes];
        }
        $hashesToUrls = [];
        foreach ($hashes as $hash) {
            if (! $this->dao->isUploaded($hash)) {
                throw new BusinessException(ErrorCode::FILE_HAS_NOT_BEEN_UPLOADED);
            }
            $file = $this->getFileInfoByHash($hash);
            $hashesToUrls = Arr::merge($hashesToUrls, [$hash => Arr::get($file, 'url')]);
        }
        // 获取数组所有value
        $urls = array_values($hashesToUrls);
        try {
            $ossRamService = make(OssRamService::class, ['option' => $this->config->get('sts')]);
            return Arr::merge(['objects' => $hashesToUrls], $ossRamService->allowGetObject($urls));
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::GET_STS_TOKEN_FAIL);
        }
    }

    #[CacheEvict(prefix: 'fileInfoByHash', value: 'fileHash_#{hash}')]
    public function uploaderCallback(string $hash): bool
    {
        return $this->dao->changeStatusByHash($hash, UploadStatusCode::UPLOAD_FINISHED);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function uploaderPreparation(array $metadata, array $config): array
    {
        /**
         * todo::根据当前upload-tool的storage-mode去获取sts-factory对应的sts适配器实例,获取失败则判断不支持sts.
         */
        if ($this->uploadTool->getStorageMode() != FileSystemCode::OSS->value) {
            throw new BusinessException(ErrorCode::NOT_SUPPORT);
        }
        $hash = md5($this->fitterMd5Resource($metadata));
        ! $hash && throw new BusinessException(ErrorCode::HASH_VERIFICATION_FAILED);
        $data = ['hash' => $hash, 'is_uploaded' => $this->dao->isUploaded($hash)];
        if ($data['is_uploaded']) {
            return $data;
        }
        $fileInfo = $this->uploadTool->handlePreparation($metadata, Arr::merge($config, ['hash' => $hash]));
        $this->save($fileInfo) ?? throw new BusinessException(ErrorCode::UPLOAD_FAILED);
        return $data;
    }

    #[Cacheable(prefix: 'fileInfoByHash', value: 'fileHash_#{hash}', ttl: 3600 * 24)]
    public function getFileInfoByHash(string $hash): array
    {
        $file = $this->dao->getFileInfoByHash($hash);
        if (is_null($file)) {
            throw new BusinessException(ErrorCode::FILE_NOT_EXIST);
        }
        return $file;
    }

    #[CacheEvict(prefix: 'fileInfoByHash', value: 'fileHash_#{hash}')]
    public function updateByHash(string $hash, array $data): bool
    {
        $id = $this->dao->value(['hash' => $hash]);
        return parent::update($id, $data);
    }

    /**
     * @param mixed $config
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    private function generateSignature(string $url, array $config = []): string
    {
        return $this->uploadTool->getFileSystem()->temporaryUrl($url, Carbon::now()->addHour(), $config);
    }

    /**
     * 利用mate信息生成md5.
     */
    private function fitterMd5Resource(array $metadata): bool|string
    {
        return json_encode(['user_id' => user()->getId(), 'size_byte' => $metadata['size_byte'], 'mime_type' => $metadata['mime_type'], 'last_modified' => $metadata['last_modified']]);
    }
}
