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

    // user
    'user_not_found' => '用户不存在',
    'user_not_login' => '用户未登录',
    'user_login_failed' => '用户登录失败',
    'user_ban' => '用户被禁用',

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
];
