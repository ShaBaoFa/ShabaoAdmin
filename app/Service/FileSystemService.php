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
use Hyperf\Collection\Arr;
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
use Wlfpanda1012\AliyunSts\Constants\OSSClientCode;
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
    #[Cacheable(prefix: 'uploaderStsToken', value: 'fileHash_#{hash}', ttl: 900)]
    public function getUploaderStsToken(string $hash): array
    {
        if ($this->dao->isUploaded($hash)) {
            throw new BusinessException(ErrorCode::FILE_HAS_BEEN_UPLOADED);
        }
        $fileInfo = $this->dao->getFileInfoByHash($hash);
        try {
            $sts = $this->config->get('sts');
            $ossRamService = make(OssRamService::class, ['option' => $sts]);
            $customParams = ['hash' => $hash];
            $this->generateOssCallback($customParams);
            return ['callback_custom_params' => $customParams, 'credentials' => $ossRamService->allowPutObject($fileInfo['url'])];
        } catch (Exception $e) {
            throw new BusinessException(ErrorCode::GET_STS_TOKEN_FAIL);
        }
    }

    public function getDownloaderStsToken(array|string $hash): array
    {
        if (! Arr::accessible($hash)) {
            $hash = [$hash];
        }
        if (! $this->dao->areUploaded($hash)) {
            throw new BusinessException(ErrorCode::FILE_HAS_NOT_BEEN_UPLOADED);
        }
        $hashesToUrls = $this->dao->getFilesUrlByHash($hash);
        // 获取数组所有value
        $urls = array_values($hashesToUrls);
        try {
            $ossRamService = make(OssRamService::class, ['option' => $this->config->get('sts')]);
            return Arr::merge(['objects' => $hashesToUrls], $ossRamService->allowGetObject((array) $urls));
        } catch (Exception $e) {
            var_dump($e->getMessage());
            throw new BusinessException(ErrorCode::GET_STS_TOKEN_FAIL);
        }
    }

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
            throw new BusinessException(ErrorCode::STS_NOT_SUPPORT);
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

    private function generateOssCallback(array $customParams = []): array
    {
        $sts = $this->config->get('sts');
        $callback = $sts['oss']['callback'];
        ! json_encode($callback) ?? throw new BusinessException(ErrorCode::SERVER_ERROR);
        if (empty($customParams)) {
            return [OSSClientCode::OSS_CALLBACK->value => json_encode($callback)];
        }
        $callback[OSSClientCode::OSS_CALLBACK_BODY->value] = $this->generateOssCallbackBody($customParams);
        return [
            OSSClientCode::OSS_CALLBACK->value => json_encode($callback),
            OSSClientCode::OSS_CALLBACK_VAR->value => $this->generateOssCallbackVar($customParams),
        ];
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

    private function fitterMd5Resource(array $metadata): bool|string
    {
        return json_encode(['user_id' => user()->getId(), 'size_byte' => $metadata['size_byte'], 'mime_type' => $metadata['mime_type'], 'last_modified' => $metadata['last_modified']]);
    }

    /**
     * (不太用的上,前端的格式略微不同).
     */
    private function generateOssCallbackBody(?array $customParams = null): string
    {
        $sts = $this->config->get('sts');
        $callback = $sts['oss']['callback'];
        $baseParams = is_string($callback[OSSClientCode::OSS_CALLBACK_BODY->value]) ? explode(OSSClientCode::OSS_CALLBACK_SEPARATOR->value, $callback[OSSClientCode::OSS_CALLBACK_BODY->value]) : $callback[OSSClientCode::OSS_CALLBACK_BODY->value];
        ! is_array($baseParams) && throw new BusinessException(ErrorCode::SERVER_ERROR);

        // 遍历传入的数组，将其格式化为 'key=value' 的形式
        foreach ($customParams as $key => $value) {
            $variable = '${' . OSSClientCode::OSS_CALLBACK_CUSTOM_VAR_PREFIX->value . $key . '}';
            $baseParams[] = "{$key}={$variable}";
        }

        // 将所有参数用 & 连接成一个字符串
        return implode(OSSClientCode::OSS_CALLBACK_SEPARATOR->value, $baseParams);
    }

    /**
     * (不太用的上,前端的格式略微不同).
     * @param mixed $customParams
     */
    private function generateOssCallbackVar($customParams): bool|string
    {
        // 设置发起回调请求的自定义参数，由Key和Value组成，Key必须以枚举指定的前缀开始。
        $var = [];
        foreach ($customParams as $key => $value) {
            $var[OSSClientCode::OSS_CALLBACK_CUSTOM_VAR_PREFIX->value . $key] = $value;
        }
        return json_encode($var);
    }
}
