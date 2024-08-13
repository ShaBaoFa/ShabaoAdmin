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
enum ProduceStatusCode: int
{
    use EnumConstantsTrait;

    #[Message('common.produce_status_waiting')]
    case PRODUCE_STATUS_WAITING = 1;

    #[Message('common.produce_status_doing')]
    case PRODUCE_STATUS_DOING = 2;

    #[Message('common.produce_status_success')]

    case PRODUCE_STATUS_SUCCESS = 3;

    #[Message('common.produce_status_fail')]

    case PRODUCE_STATUS_FAIL = 4;

    #[Message('common.produce_status_repeat')]
    case PRODUCE_STATUS_REPEAT = 5;
}
