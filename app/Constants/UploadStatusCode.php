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
enum UploadStatusCode: int
{
    use EnumConstantsTrait;

    #[Message('common.finished')]
    case UPLOAD_FINISHED = 1;

    #[Message('common.unfinished')]
    case UPLOAD_UNFINISHED = 2;
}
