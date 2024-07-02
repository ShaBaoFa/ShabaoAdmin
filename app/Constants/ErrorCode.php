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

    # 导出数据失败
    #[Message('EXPORT_DATA_FAILED')]
    case EXPORT_DATA_FAILED = 20002;

    # 导出未指定DTO
    #[Message('EXPORT_DTO_NOT_SPECIFIED')]
    case EXPORT_DTO_NOT_SPECIFIED = 20001;

    # DTO不符合规范
    #[Message('DTO_NOT_IMPLEMENT_MODEL_EXCEL')]
    case DTO_NOT_IMPLEMENT_MODEL_EXCEL = 20003;

    # DTO注解信息为空（dto annotation info is empty）
    #[Message('DTO_ANNOTATION_INFO_EMPTY')]
    case DTO_ANNOTATION_INFO_EMPTY = 20004;

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
