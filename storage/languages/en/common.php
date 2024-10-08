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
    'not_found' => 'not_found',

    // user
    'user_not_found' => 'user_not_found',
    'user_not_login' => 'user_not_login',
    'user_login_failed' => 'user_login_failed',
    'user_ban' => 'user_ban',
    'user_login_type_error' => 'user_login_type_error',

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
    'file_has_not_been_uploaded' => 'file_has_not_been_uploaded',
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

    // produce
    'produce_status_waiting' => 'produce_status_waiting',
    'produce_status_doing' => 'produce_status_doing',
    'produce_status_success' => 'produce_status_success',
    'produce_status_fail' => 'produce_status_fail',
    'produce_status_repeat' => 'produce_status_repeat',

    // consume
    'consume_status_no' => 'consume_status_no',
    'consume_status_doing' => 'consume_status_doing',
    'consume_status_success' => 'consume_status_success',
    'consume_status_fail' => 'consume_status_fail',
    'consume_status_repeat' => 'consume_status_repeat',

    // message
    'message_notice' => 'message_notice',
    'message_announcement' => 'message_announcement',
    'message_todo' => 'message_todo',
    'message_copy_mine' => 'message_copy_mine',
    'message_private_message' => 'message_private_message',
    'message_cannot_send_to_yourself' => 'message_cannot_send_to_yourself',
    'message_read' => 'message_read',
    'message_unread' => 'message_unread',
    // ws_event
    'ev_new_message' => 'you have new message',
    'ev_new_private_message' => 'you have new private message',
    'ev_user_kick_out' => 'you have been kicked out',
    // disk
    'disk_file_not_exist' => 'disk_file_not_exist',
    'disk_folder_not_exist' => 'disk_folder_not_exist',
    'disk_folder_illegal_selected' => 'disk_folder_illegal_selected',
    'disk_file_illegal_share' => 'disk_file_illegal_share',
    'disk_cannot_share_to_yourself' => 'disk_cannot_share_to_yourself',
    'disk_share_password_error' => 'disk_share_password_error',
];
