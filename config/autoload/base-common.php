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
    /**
     * 是否打印sql.
     */
    'sql_log_enabled' => \Hyperf\Support\env('SQL_LOG_ENABLED', false),
    /**
     * 是否启用数据权限.
     */
    'data_scope_enabled' => \Hyperf\Support\env('DATA_SCOPE_ENABLED', true),
    /**
     * excel 导入、导出驱动类型 auto, xlsWriter, phpOffice
     * auto 优先使用xlsWriter，若环境没有安装xlsWriter扩展则使用phpOffice.
     */
    'excel_drive' => 'auto',
    /**
     * 生成RESTFUL按钮菜单.
     */
    'restful_menu_enabled' => \Hyperf\Support\env('RESTFUL_MENU_ENABLED', false),
    /**
     * 组织超级管理员角色ID.
     */
    'org_super_role_id' => \Hyperf\Support\env('ORG_SUPER_ROLE', 2),
    /**
     * 默认密码
     */
    'default_password' => \Hyperf\Support\env('DEFAULT_PASSWORD', 'Admin@2024!'),
    /**
     * 上传地址
     */
    'update_path' => \Hyperf\Support\env('UPDATE_PATH', 'uploadFile'),
    /**
     * 队列交换机.
     */
    'queue_exchange' => \Hyperf\Support\env('QUEUE_EXCHANGE', 'web-api'),
    /**
     * 延时队列交换机.
     */
    'delay_queue_exchange' => \Hyperf\Support\env('DELAY_QUEUE_EXCHANGE', 'delay.web-api'),
];
