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
enum QueueMesContentTypeCode: int
{
    use EnumConstantsTrait;

    // 消息类
    #[Message('common.queue_notice')]
    case TYPE_NOTICE = 1001;

    #[Message('common.queue_announcement')]
    case TYPE_ANNOUNCE = 1002;

    #[Message('common.queue_todo')]
    case TYPE_TODO = 1003;

    #[Message('common.queue_copy_mine')]
    case TYPE_COPY_MINE = 1004;

    #[Message('common.queue_private_message')]
    case TYPE_PRIVATE_MESSAGE = 1005;

    // 订单类

    // 系统类

    // 其他类
}
