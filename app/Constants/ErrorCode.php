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

    #[Message('common.unauthorized')]
    case UNAUTHORIZED = 401;

    #[Message('common.forbidden')]
    case FORBIDDEN = 403;

    #[Message('common.invalid_params')]
    case INVALID_PARAMS = 422;

    #[Message('common.server_error')]
    case SERVER_ERROR = 500;

    #[Message('common.user_not_found')]
    case USER_NOT_EXIST = 10001;

    #[Message('common.user_login_failed')]
    case USER_PASSWORD_ERROR = 10002;

    #[Message('common.user_ban')]
    case USER_BAN = 10003;

    #[Message('common.user_not_login')]
    case NO_LOGIN_USER = 10004;

    # 导出数据失败
    #[Message('common.export_data_failed')]
    case EXPORT_DATA_FAILED = 20002;

    # 导出未指定DTO
    #[Message('common.export_dto_not_specified')]
    case EXPORT_DTO_NOT_SPECIFIED = 20001;

    # DTO不符合规范
    #[Message('common.dto_not_implement_model_excel')]
    case DTO_NOT_IMPLEMENT_MODEL_EXCEL = 20003;

    # DTO注解信息为空（dto annotation info is empty）
    #[Message('common.dto_annotation_info_is_empty')]
    case DTO_ANNOTATION_INFO_EMPTY = 20004;

    #[Message('common.role_not_found')]
    case ROLE_CODE_NOT_EXIST = 30001;

    #[Message('common.menu_not_found')]
    case MENU_CODE_NOT_EXIST = 40001;

    public function getMessage(?array $translate = null): string
    {
        $arguments = [];
        if ($translate) {
            $arguments = [$translate];
        }

        return $this->__call('getMessage', $arguments);
    }
}
