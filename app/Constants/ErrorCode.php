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

    #[Message('common.dept_parent_not_valid')]
    case DEPT_PARENT_NOT_VALID = 50001;

    #[Message('common.dept_can_not_delete')]
    case DEPT_CAN_NOT_DELETE = 50002;

    #[Message('common.org_parent_not_valid')]
    case ORG_PARENT_NOT_VALID = 60001;

    #[Message('common.org_can_not_delete')]
    case ORG_CAN_NOT_DELETE = 60002;

    #[Message('common.upload_verification_failed')]
    case UPLOAD_VERIFICATION_FAILED = 70001;

    #[Message('common.upload_failed')]
    case UPLOAD_FAILED = 70002;

    #[Message('common.hash_verification_failed')]
    case HASH_VERIFICATION_FAILED = 70003;

    #[Message('common.file_not_exist')]
    case FILE_NOT_EXIST = 70004;

    #[Message('common.file_too_large_to_read')]
    case FILE_TOO_LARGE_TO_READ = 70005;

    #[Message('common.sts_not_support')]
    case STS_NOT_SUPPORT = 70006;

    #[Message('common.file_has_been_uploaded')]
    case FILE_HAS_BEEN_UPLOADED = 70007;

    #[Message('common.get_sts_token_fail')]
    case GET_STS_TOKEN_FAIL = 70008;

    #[Message('common.file_has_not_been_uploaded')]
    case FILE_HAS_NOT_BEEN_UPLOADED = 70009;

    // QueueLogService
    #[Message('common.queue_not_enable')]
    case QUEUE_NOT_ENABLE = 80001;

    #[Message('common.queue_missing_message')]
    case QUEUE_MISSING_MESSAGE = 80002;

    public function getMessage(?array $translate = null): string
    {
        $arguments = [];
        if ($translate) {
            $arguments = [$translate];
        }

        return $this->__call('getMessage', $arguments);
    }
}
