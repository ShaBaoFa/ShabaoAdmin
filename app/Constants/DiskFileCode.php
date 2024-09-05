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
enum DiskFileCode: int
{
    use EnumConstantsTrait;

    // type (1: 文件夹 2: 文件)
    case TYPE_FOLDER = 1;
    case TYPE_FILE = 2;

    // file_type (21: 图片 22: 视频 23: 音频 24: 文档 25: 其他)
    case FILE_TYPE_IMAGE = 21;
    case FILE_TYPE_VIDEO = 22;
    case FILE_TYPE_AUDIO = 23;
    case FILE_TYPE_DOCUMENT = 24;
    case FILE_TYPE_OTHER = 25;
}
