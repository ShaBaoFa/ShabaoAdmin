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
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum FileSystemCode: int
{
    use EnumConstantsTrait;

    #[Message('common.local')]
    case LOCAL = 1;

    #[Message('common.oss')]
    case OSS = 2;

    #[Message('common.qiniu')]
    case QINIU = 3;

    #[Message('common.cos')]
    case COS = 4;

    #[Message('common.ftp')]
    case FTP = 5;

    #[Message('common.memory')]
    case MEMORY = 6;

    #[Message('common.s3')]
    case S3 = 7;

    #[Message('common.minio')]
    case MINIO = 8;
}
