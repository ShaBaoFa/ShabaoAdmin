<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum ErrorCode: int implements ErrorCodeInterface
{
    use EnumConstantsTrait;

    #[Message('Server Error')]
    case SERVER_ERROR = 500;

    #[Message('USER_NOT_EXIST')]
    case USER_NOT_EXIST = 10001;

    #[Message('USER_PASSWORD_ERROR')]
    case USER_PASSWORD_ERROR = 10002;

    #[Message('USER_BAN')]
    case USER_BAN = 10003;

    #[Message('UNAUTHORIZED')]
    case UNAUTHORIZED = 401;

    #[Message('INVALID_PARAMS')]
    case INVALID_PARAMS = 422;

    public function getMessage(?array $translate = null): string
    {
        $arguments = [];
        if ($translate) {
            $arguments = [$translate];
        }

        return $this->__call('getMessage', $arguments);
    }
}
