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
enum WsEventCode: string
{
    use EnumConstantsTrait;

    case GET_UNREAD_MESSAGE = 'get_unread_message';

    #[Message('common.ev_new_message')]
    case EV_NEW_MESSAGE = 'ev_new_message';

    #[Message('common.ev_new_private_message')]
    case EV_NEW_PRIVATE_MESSAGE = 'ev_new_private_message';

    #[Message('common.ev_user_kick_out')]
    case EV_USER_KICK_OUT = 'ev_user_kick_out';
}
