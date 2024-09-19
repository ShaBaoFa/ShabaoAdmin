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

use App\Amqp\Consumer\OssProcessConsumer;
use App\Amqp\Producer\OssProcessProducer;
use App\Base\BaseService;
use App\Base\BaseUpload;
use App\Constants\DiskFileCode;
use App\Constants\ErrorCode;
use App\Constants\FileSystemCode;
use App\Constants\UploadCode;
use App\Constants\UploadStatusCode;
use App\Dao\UploadFileDao;
use App\Exception\BusinessException;
use App\Vo\AmqpQueueVo;
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
use OSS\Core\OssException;
use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use Swoole\Coroutine\System;
use Wlfpanda1012\AliyunSts\Constants\OSSClientCode;
use Wlfpanda1012\AliyunSts\Oss\OssRamService;

use function App\Helper\base64url_encode;
use function App\Helper\user;
use function Hyperf\Config\config;
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
            $this->generateOssCallback($customParams);
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

    /**
     * @throws RedisException
     * @throws RequestCore_Exception
     * @throws ContainerExceptionInterface
     * @throws OssException
     * @throws NotFoundExceptionInterface
     */
    public function getPreview(string $hash): array
    {
        $file = $this->getFileInfoByHash($hash);
        if (! Arr::get($file, 'preview_url')) {
            [$config,$saveObj] = $this->processForPreview($file);
            // 是否可以持久化判断
            if ($this->canPersistence($file)) {
                // 是否需要同步持久化
                var_dump('可以持久化');
                $needSync = true;
                if (config('amqp.enable') && di()->get(OssProcessConsumer::class)->isEnable()) {
                    // 通过队列去处理预览持久化
                    $amqpQueueVo = new AmqpQueueVo();
                    $amqpQueueVo->setProducer(OssProcessProducer::class);
                    $queueData = [
                        'id' => Arr::get($file, 'id'),
                        'config' => $config,
                        'save_obj' => $saveObj,
                        'type' => $this->checkSuffix(Arr::get($file, 'suffix')),
                    ];
                    $amqpQueueVo->setData($queueData);
                    if (di()->get(QueueLogService::class)->addQueue($amqpQueueVo)) {
                        $needSync = false;
                    }
                }
                if ($needSync) {
                    $this->saveAs($file, $config, $saveObj);
                }
            }
            return [
                'preview_url' => $this->generateSignature(Arr::get($file, 'url'), $config),
                'type' => $this->checkSuffix(Arr::get($file, 'suffix')),
                'is_persistence' => false,
            ];
        }
        $config = [];
        if ($this->checkSuffix(Arr::get($file, 'suffix')) == DiskFileCode::FILE_TYPE_VIDEO) {
            $config = [OssClient::OSS_PROCESS => 'hls/sign,live_1'];
        }
        return [
            'preview_url' => $this->generateSignature(Arr::get($file, 'preview_url'), $config),
            'type' => $this->checkSuffix(Arr::get($file, 'suffix')),
            'is_persistence' => true,
        ];
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws ContainerExceptionInterface
     */
    public function getThumbnail(string $hash): array
    {
        $file = $this->getFileInfoByHash($hash);
        if (! Arr::get($file, 'thumb_url')) {
            $config = $this->processForPreview($file);
            return ['thumb_url' => $this->generateSignature(Arr::get($file, 'url'), $config)];
        }
        return ['thumb_url' => $this->generateSignature(Arr::get($file, 'thumb_url'))];
    }

    #[CacheEvict(prefix: 'fileInfoByHash', value: 'fileHash_#{hash}')]
    public function updateByHash(string $hash, array $data): bool
    {
        $id = $this->dao->value(['hash' => $hash]);
        return parent::update($id, $data);
    }

    /**
     * @throws OssException
     * @throws RequestCore_Exception
     */
    public function saveAs(array $file, array $config, string $saveObj): bool
    {
        $ossConfig = config('file.storage.oss');
        $ossClient = new OssClient(Arr::get($ossConfig, 'accessId'), Arr::get($ossConfig, 'accessSecret'), Arr::get($ossConfig, 'endpoint'));
        $process = Arr::get($config, OssClient::OSS_PROCESS) .
            '|sys/saveas' .
            ',o_' . base64url_encode($saveObj) .
            ',b_' . base64url_encode(Arr::get($ossConfig, 'bucket'));
        if ($this->checkSuffix(Arr::get($file, 'suffix')) == DiskFileCode::FILE_TYPE_VIDEO) {
            $saveObj .= '.m3u8';
        }
        return ! is_null($ossClient->processObject(Arr::get($ossConfig, 'bucket'), $this->formatOssUrl(Arr::get($file, 'url')), $process)) && $this->updateByHash(Arr::get($file, 'hash'), ['preview_url' => $this->formatLocalUrl($saveObj)]);
    }

    private function canPersistence(array $file): bool
    {
        // 目前 OSS 不支持文档预览的持久化
        if (Arr::get($file, 'storage_mode') != FileSystemCode::OSS->value
            && $this->checkSuffix(Arr::get($file, 'suffix')) == DiskFileCode::FILE_TYPE_DOCUMENT) {
            return false;
        }
        return true;
    }

    private function formatOssUrl(string $url): string
    {
        // url 以 ‘/’ 开头则删除 /
        return $url[0] === '/' ? substr($url, 1) : $url;
    }

    private function formatLocalUrl(string $url): string
    {
        return $url[0] === '/' ? $url : '/' . $url;
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
     * @param mixed $config
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    private function generateSignature(string $url, array $config = []): string
    {
        $config['response-content-disposition'] = 'inline';
        return $this->uploadTool->getFileSystem()->temporaryUrl($url, Carbon::now()->addHour(), $config);
    }

    private function processForPreview(array $file): array
    {
        $watermark = di()->get(UserService::class)->value(['id' => Arr::get($file, $this->dao->getModel()->getDataScopeField())], 'username');
        $mode = Arr::get($file, 'storage_mode');
        $saveObj = $this->getPersistenceName($file);
        $config = match ($this->checkSuffix(Arr::get($file, 'suffix'))) {
            DiskFileCode::FILE_TYPE_IMAGE => $this->imageProcessForPreview($mode, $watermark),
            DiskFileCode::FILE_TYPE_DOCUMENT => $this->docProcessForPreview($mode, $watermark),
            DiskFileCode::FILE_TYPE_VIDEO => $this->videoProcessForPreview($mode),
            default => throw new BusinessException(ErrorCode::NOT_SUPPORT)
        };
        return [$config, $saveObj];
    }

    private function getPersistenceName(array $file): string
    {
        return match ($this->checkSuffix(Arr::get($file, 'suffix'))) {
            DiskFileCode::FILE_TYPE_VIDEO => $this->formatVideoPreviewUrl(Arr::get($file, 'url')),
            default => $this->formatCommonPreviewUrl(Arr::get($file, 'url'))
        };
    }

    private function formatCommonPreviewUrl(string $url): string
    {
        return UploadCode::PREVIEW_PREFIX->value . $url;
    }

    private function formatVideoPreviewUrl(string $url): string
    {
        $oUrl = UploadCode::PREVIEW_PREFIX->value . $url;
        // 去掉后缀名
        return substr($oUrl, 0, strrpos($oUrl, '.'));
    }

    private function checkSuffix(string $suffix): DiskFileCode
    {
        return match (true) {
            in_array($suffix, $this->getImagesSuffix()) => DiskFileCode::FILE_TYPE_IMAGE,
            in_array($suffix, $this->getDocsSuffix()) => DiskFileCode::FILE_TYPE_DOCUMENT,
            in_array($suffix, $this->getVideosSuffix()) => DiskFileCode::FILE_TYPE_VIDEO,
            default => throw new BusinessException(ErrorCode::NOT_SUPPORT)
        };
    }

    private function getImagesSuffix(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    }

    private function getDocsSuffix(): array
    {
        return ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    }

    private function getVideosSuffix(): array
    {
        return ['mp4', 'mkv', 'avi'];
    }

    private function imageProcessForPreview($mode, string $watermark): array
    {
        return match ($mode) {
            FileSystemCode::OSS->value => [OssClient::OSS_PROCESS => 'image/auto-orient,1/interlace,1/resize,p_50/quality,q_90/watermark,text_' . base64url_encode($watermark) . ',type_d3F5LXplbmhlaQ,color_000000,size_40,rotate_156,fill_1,t_26,x_10,y_10'],
            default => []
        };
    }

    private function videoProcessForPreview($mode): array
    {
        /**
         * 生成边转边播播放列表.
         */
        return match ($mode) {
            FileSystemCode::OSS->value => [OssClient::OSS_PROCESS => 'hls/m3u8,vcodec_h264,fps_25,fpsopt_1,s_960x,sopt_1,scaletype_fit,arotate_1,crf_26,acodec_aac,ar_44100,ac_2,ab_128000,abopt_1,st_5000,initd_30000'],
            default => []
        };

        /**
         * 使用hls/sign签名边转边播.
         */
    }

    private function docProcessForPreview($mode, string $watermark): array
    {
        return match ($mode) {
            FileSystemCode::OSS->value => [OssClient::OSS_PROCESS => 'doc/preview,export_0,print_0,copy_0/watermark,text_' . base64url_encode($watermark) . ',color_#FFFFFF,rotate_156,size_30,t_60'],
            default => []
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
