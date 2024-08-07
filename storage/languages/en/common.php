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
return [
    // base
    'normal' => 'normal',
    'abnormal' => 'abnormal',
    // server
    'unauthorized' => 'unauthorized',
    'forbidden' => 'forbidden',
    'invalid_params' => 'invalid_params',
    'server_error' => 'server_error',

    // user
    'user_not_found' => 'user_not_found',
    'user_not_login' => 'user_not_login',
    'user_login_failed' => 'user_login_failed',
    'user_ban' => 'user_ban',

    // excel
    'export_data_failed' => 'export_data_failed',
    'export_dto_not_specified' => 'export_dto_not_specified',
    'dto_not_implement_model_excel' => 'dto_not_implement_model_excel',
    'dto_annotation_info_is_empty' => 'dto_annotation_info_is_empty',

    // role
    'role_not_found' => 'role_not_found',

    // menu
    'menu_not_found' => 'menu_not_found',

    // dept
    'dept_parent_not_valid' => 'dept_parent_not_valid',
    'dept_can_not_delete' => 'dept_can_not_delete',

    // org
    'org_parent_not_valid' => 'org_parent_not_valid',
    'org_can_not_delete' => 'org_can_not_delete',

    // upload
    'upload_verification_failed' => 'upload_verification_failed',
    'upload_failed' => 'upload_failed',
    'hash_verification_failed' => 'hash_verification_failed',
    'file_not_exist' => 'file_not_exist',
    'file_too_large_to_read' => 'file_too_large_to_read',
    'sts_not_support' => 'sts_not_support',
    'finished' => 'finished',
    'unfinished' => 'unfinished',
    'file_has_been_uploaded' => 'file_has_been_uploaded',
    'get_sts_token_fail' => 'get_sts_token_fail',

    // filesystem
    'local' => 'local',
    'oss' => 'oss',
    'qiniu' => 'qiniu',
    'cos' => 'cos',
    'ftp' => 'ftp',
    'memory' => 'memory',
    's3' => 's3',
    'minio' => 'minio',
];
