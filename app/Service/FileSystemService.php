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
use League\Flysystem\FilesystemException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

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
    public function getFileByHash(string $hash, array $columns = ['*']): array
    {
        $data = $this->dao->getFileInfoByHash($hash, $columns);
        if (empty($data)) {
            return [];
        }
        match ($data['storage_mode']) {
            FileSystemCode::OSS->value => $data['url'] = $this->generateSignature($data),
            default => null
        };
        return $data;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    private function generateSignature(array $data): string
    {
        $storageMode = Str::lower(FileSystemCode::tryFrom($data['storage_mode'])->name ?? FileSystemCode::LOCAL->name);
        $filesystem = di()->get(FilesystemFactory::class)->get($storageMode);
        return match ($data['storage_mode']) {
            FileSystemCode::OSS->value => $filesystem->temporaryUrl($data['url'], Carbon::now()->addHour()),
            default => $data['url']
        };
    }
}
