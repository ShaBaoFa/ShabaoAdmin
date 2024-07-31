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

namespace App\Constants;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum FileSystemCode: int
{
    use EnumConstantsTrait;

    case LOCAL = 1;
    case OSS = 2;
    case QINIU = 3;
    case COS = 4;
    case FTP = 5;
    case MEMORY = 6;
    case S3 = 7;
    case MINIO = 8;

    public function getMessage(?array $translate = null): string
    {
        return match ($this) {
            self::LOCAL => '本地',
            self::OSS => '阿里云OSS',
            self::FTP => 'FTP',
            self::MEMORY => '内存',
            self::S3 => 'S3',
            self::MINIO => 'Minio',
            self::QINIU => '七牛云',
            self::COS => '腾讯云COS',
        };
    }
}
