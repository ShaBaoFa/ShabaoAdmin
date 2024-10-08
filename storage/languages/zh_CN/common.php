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
    'normal' => '正常',
    'ban' => '禁用',
    // server
    'unauthorized' => '未经授权',
    'forbidden' => '禁止访问',
    'invalid_params' => '无效参数',
    'server_error' => '服务器错误',
    'not_found' => '资源未找到',

    // user
    'user_not_found' => '用户不存在',
    'user_not_login' => '用户未登录',
    'user_login_failed' => '用户登录失败',
    'user_ban' => '用户被禁用',
    'user_login_type_error' => '不正确的登陆方式',

    // excel
    'export_data_failed' => '导出数据失败',
    'export_dto_not_specified' => '导出未指定DTO',
    'dto_not_implement_model_excel' => 'DTO不符合规范',
    'dto_annotation_info_is_empty' => 'DTO注解信息为空',

    // role
    'role_not_found' => '角色不存在',

    // menu
    'menu_not_found' => '菜单不存在',

    // dept
    'dept_parent_not_valid' => '上级部门不合法',
    'dept_can_not_delete' => '部门不可被删除',

    // org
    'org_parent_not_valid' => '上级组织不合法',
    'org_can_not_delete' => '组织不可被删除',

    // upload
    'upload_verification_failed' => '文件验证失败',
    'upload_failed' => '文件上传失败',
    'hash_verification_failed' => '文件哈希验证失败',
    'file_not_exist' => '文件不存在',
    'file_too_large_to_read' => '文件过大，请更换方式读取',
    'sts_not_support' => 'STS不支持',
    'finished' => '已完成',
    'unfinished' => '未完成',
    'file_has_been_uploaded' => '文件已上传',
    'file_has_not_been_uploaded' => '文件未上传',
    'get_sts_token_fail' => '获取STS令牌失败',

    // filesystem
    'local' => '本地',
    'oss' => '阿里云OSS',
    'qiniu' => '七牛云',
    'cos' => '腾讯云COS',
    'ftp' => 'FTP',
    'memory' => '内存',
    's3' => 'S3',
    'minio' => 'Minio',

    // produce
    'produce_status_waiting' => '待生产',
    'produce_status_doing' => '生产中',
    'produce_status_success' => '生产成功',
    'produce_status_fail' => '生产失败',
    'produce_status_repeat' => '重复生产',

    // consume
    'consume_status_no' => '未消费',
    'consume_status_doing' => '消费中',
    'consume_status_success' => '消费成功',
    'consume_status_fail' => '消费失败',
    'consume_status_repeat' => '重复消费',

    // message
    'message_notice' => '通知',
    'message_announcement' => '公告',
    'message_todo' => '待办',
    'message_copy_mine' => '抄送我的',
    'message_private_message' => '私信',
    'message_cannot_send_to_yourself' => '不能发送给自己',
    'message_read' => '已读',
    'message_unread' => '未读',

    // ws_event
    'ev_new_message' => '您有新的消息',
    'ev_new_private_message' => '您有新的私信',
    'ev_user_kick_out' => '用户被踢出',

    // disk
    'disk_file_not_exist' => '文件不存在',
    'disk_folder_not_exist' => '文件夹不存在',
    'disk_folder_illegal_selected' => '不合法的文件夹选择',
    'disk_file_illegal_share' => '不合法的文件分享',
    'disk_cannot_share_to_yourself' => '不能分享给自己',
    'disk_share_password_error' => '分享密码错误',
];
