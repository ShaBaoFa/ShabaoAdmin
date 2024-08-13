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
enum ConsumerStatusCode: int
{
    use EnumConstantsTrait;

    #[Message('common.consume_status_no')]
    case CONSUME_STATUS_NO = 1;

    #[Message('common.consume_status_doing')]
    case CONSUME_STATUS_DOING = 2;

    #[Message('common.consume_status_success')]
    case CONSUME_STATUS_SUCCESS = 3;

    #[Message('common.consume_status_fail')]
    case CONSUME_STATUS_FAIL = 4;

    #[Message('common.consume_status_repeat')]
    case CONSUME_STATUS_REPEAT = 5;
}
