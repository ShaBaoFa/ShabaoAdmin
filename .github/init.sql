create database if not exists `hyperf` default character set utf8mb4 collate utf8mb4_unicode_ci;
use `hyperf`;
create table if not exists department_organization
(
    department_id   bigint unsigned not null comment '部门主键',
    organization_id bigint unsigned not null comment '组织主键',
    primary key (department_id, organization_id)
)
    comment '部门与组织关联表';

create table if not exists department_role
(
    role_id       bigint unsigned not null comment '角色主键',
    department_id bigint unsigned not null comment '部门主键',
    primary key (role_id, department_id)
)
    comment '角色与部门关联表';

create table if not exists department_user
(
    user_id       bigint unsigned not null comment '用户主键',
    department_id bigint unsigned not null comment '部门主键',
    primary key (user_id, department_id)
)
    comment '用户与部门关联表';

create table if not exists departments
(
    id         bigint unsigned auto_increment
        primary key,
    parent_id  bigint unsigned               not null comment '父ID',
    level      varchar(500)                  not null comment '组级集合',
    name       varchar(30)                   not null comment '部门名称',
    leader     varchar(20)                   null comment '负责人',
    phone      varchar(11)                   null comment '联系电话',
    status     smallint          default 1   null comment '状态 (1正常 2停用)',
    sort       smallint unsigned default '0' null comment '排序',
    created_by bigint                        null comment '创建者',
    updated_by bigint                        null comment '更新者',
    remark     varchar(255)                  null comment '备注',
    created_at datetime                      null,
    updated_at datetime                      null,
    deleted_at timestamp                     null
);

create index  departments_created_by_index
    on departments (created_by);

create index departments_parent_id_index
    on departments (parent_id);

create index departments_status_index
    on departments (status);

create index departments_updated_by_index
    on departments (updated_by);

create table if not exists disk_files
(
    id         bigint unsigned auto_increment comment '主键'
        primary key,
    name       varchar(255)                null comment '文件(文件夹)名',
    level      varchar(500)                null comment '文件(文件夹)路径',
    hash       varchar(64)                 null comment '文件hash',
    suffix     varchar(10)                 null comment '文件后缀',
    parent_id  bigint unsigned default '0' not null comment '父ID',
    size_byte  bigint                      null comment '字节数',
    size_info  varchar(50)                 null comment '文件大小',
    type       smallint        default 2   null comment '(1: 文件夹 2: 文件)',
    file_type  smallint        default 0   null comment '(21: 图片 22: 视频 23: 音频 24: 文档 25: 其他)',
    created_by bigint                      null comment '创建者',
    updated_by bigint                      null comment '更新者',
    created_at timestamp                   null comment '创建时间',
    updated_at timestamp                   null comment '更新时间',
    deleted_at timestamp                   null comment '删除时间',
    remark     varchar(255)                null comment '备注'
)
    comment '云盘表';

create index disk_files_file_type_index
    on disk_files (file_type);

create index disk_files_parent_id_index
    on disk_files (parent_id);

create index disk_files_type_index
    on disk_files (type);

create table if not exists exhibition_hall
(
    exhibition_id bigint unsigned not null comment '展会主键',
    hall_id       bigint unsigned not null comment '展馆主键',
    primary key (exhibition_id, hall_id)
)
    comment '展馆与展会关联表';

create table if not exists exhibitions
(
    id         bigint unsigned auto_increment
        primary key,
    status     smallint          default 1   null comment '状态 (1正常 2停用)',
    sort       smallint unsigned default '0' null comment '排序',
    created_by bigint                        null comment '创建者',
    updated_by bigint                        null comment '更新者',
    created_at timestamp                     null,
    updated_at timestamp                     null,
    deleted_at timestamp                     null comment '删除时间',
    remark     varchar(255)                  null comment '备注'
);

create table if not exists halls
(
    id         bigint unsigned auto_increment
        primary key,
    status     smallint          default 1   null comment '状态 (1正常 2停用)',
    sort       smallint unsigned default '0' null comment '排序',
    created_by bigint                        null comment '创建者',
    updated_by bigint                        null comment '更新者',
    created_at timestamp                     null,
    updated_at timestamp                     null,
    deleted_at timestamp                     null comment '删除时间',
    remark     varchar(255)                  null comment '备注'
);

create table if not exists login_logs
(
    id          bigint unsigned auto_increment comment '主键'
        primary key,
    username    varchar(20)        not null comment '用户名',
    ip          varchar(45)        null comment '登录IP地址',
    ip_location varchar(255)       null comment 'IP所属地',
    os          varchar(50)        null comment '操作系统',
    browser     varchar(50)        null comment '浏览器',
    status      smallint default 1 not null comment '登录状态 (1成功 2失败)',
    message     varchar(50)        null comment '提示消息',
    login_time  timestamp          not null comment '登录时间',
    remark      varchar(255)       null comment '备注'
)
    comment '登录日志表';

create index login_logs_username_index
    on login_logs (username);

create table if not exists menu_role
(
    role_id bigint unsigned not null comment '角色主键',
    menu_id bigint unsigned not null comment '菜单主键',
    primary key (role_id, menu_id)
)
    comment '角色与菜单关联表';

create table if not exists menus
(
    id         bigint unsigned auto_increment comment '主键'
        primary key,
    parent_id  bigint unsigned               not null comment '父ID',
    level      varchar(500)                  not null comment '组级集合',
    name       varchar(50)                   not null comment '菜单名称',
    code       varchar(100)                  not null comment '菜单标识代码',
    icon       varchar(50)                   null comment '菜单图标',
    route      varchar(200)                  null comment '路由地址',
    component  varchar(255)                  null comment '组件路径',
    redirect   varchar(255)                  null comment '跳转地址',
    is_hidden  smallint          default 1   not null comment '是否隐藏 (1是 2否)',
    type       char              default ''  not null comment '菜单类型, (M菜单 B按钮 L链接 I iframe)',
    status     smallint          default 1   null comment '状态 (1正常 2停用)',
    sort       smallint unsigned default '0' null comment '排序',
    created_by bigint                        null comment '创建者',
    updated_by bigint                        null comment '更新者',
    created_at timestamp                     null,
    updated_at timestamp                     null,
    deleted_at timestamp                     null comment '删除时间',
    remark     varchar(255)                  null comment '备注'
)
    comment '菜单信息表';

create table if not exists message_receivers
(
    message_id  bigint unsigned    not null comment '队列消息主键',
    receiver_id bigint unsigned    not null comment '接收用户主键',
    read_status smallint default 1 null comment '已读状态 (1未读 2已读)',
    primary key (message_id, receiver_id)
)
    comment '队列消息发送接收人表';

create table if not exists messages
(
    id           bigint unsigned auto_increment comment '主键'
        primary key,
    content_type bigint unsigned null comment '内容类型',
    title        varchar(255)    null comment '消息标题',
    send_by      bigint unsigned null comment '发送人',
    receive_by   bigint unsigned null comment '接受人(私信需填)',
    content      longtext        null comment '消息内容',
    created_by   bigint          null comment '创建者',
    updated_by   bigint          null comment '更新者',
    created_at   timestamp       null comment '创建时间',
    updated_at   timestamp       null comment '更新时间',
    deleted_at   timestamp       null comment '删除时间',
    remark       varchar(255)    null comment '备注'
)
    comment '消息中心表';

create index messages_content_type_index
    on messages (content_type);

create table if not exists migrations
(
    id        int unsigned auto_increment
        primary key,
    migration varchar(255) not null,
    batch     int          not null
);

create table if not exists operation_logs
(
    id            bigint unsigned auto_increment comment '主键'
        primary key,
    username      varchar(20)  not null comment '用户名',
    method        varchar(20)  not null comment '请求方式',
    router        varchar(500) not null comment '请求路由',
    service_name  varchar(30)  not null comment '业务名称',
    ip            varchar(45)  null comment '请求IP地址',
    ip_location   varchar(255) null comment 'IP所属地',
    request_data  text         null comment '请求数据',
    response_code varchar(5)   null comment '响应状态码',
    response_data text         null comment '响应数据',
    created_by    bigint       null comment '创建者',
    updated_by    bigint       null comment '更新者',
    created_at    timestamp    null comment '创建时间',
    updated_at    timestamp    null comment '更新时间',
    deleted_at    timestamp    null comment '删除时间',
    remark        varchar(255) null comment '备注'
)
    comment '操作日志表';

create index operation_logs_username_index
    on operation_logs (username);

create table if not exists organization_user
(
    user_id         bigint unsigned not null comment '用户主键',
    organization_id bigint unsigned not null comment '组织主键',
    primary key (user_id, organization_id)
)
    comment '部门与用户关联表';

create table if not exists organizations
(
    id             bigint unsigned auto_increment
        primary key,
    parent_id      bigint unsigned    not null comment '父ID',
    super_admin_id bigint unsigned    null comment '企业超管ID',
    level          varchar(500)       not null comment '组级集合',
    name           varchar(30)        not null comment '组织名称',
    address        varchar(128)       null comment '组织地址',
    legal_person   varchar(20)        null comment '法人',
    phone          varchar(11)        null comment '组织电话',
    status         smallint default 1 null comment '状态 (1正常 2停用)',
    sort           smallint default 0 null comment '排序',
    created_by     bigint             null comment '创建者',
    updated_by     bigint             null comment '更新者',
    remark         varchar(255)       null comment '备注',
    created_at     datetime           null,
    updated_at     datetime           null,
    deleted_at     timestamp          null
);

create index organizations_created_by_index
    on organizations (created_by);

create index organizations_parent_id_index
    on organizations (parent_id);

create index organizations_status_index
    on organizations (status);

create index organizations_super_admin_id_index
    on organizations (super_admin_id);

create index organizations_updated_by_index
    on organizations (updated_by);

create table if not exists post_user
(
    user_id bigint unsigned not null comment '用户主键',
    post_id bigint unsigned not null comment '岗位主键',
    primary key (user_id, post_id)
)
    comment '用户与岗位关联表';

create table if not exists posts
(
    id         bigint unsigned auto_increment
        primary key,
    name       varchar(50)        not null comment '岗位名称',
    code       varchar(100)       not null comment '岗位代码',
    status     smallint default 1 null comment '状态 (1正常 2停用)',
    created_by bigint             null comment '创建者',
    updated_by bigint             null comment '更新者',
    remark     varchar(255)       null comment '备注',
    created_at datetime           null,
    updated_at datetime           null,
    deleted_at timestamp          null
);

create index posts_created_by_index
    on posts (created_by);

create index posts_status_index
    on posts (status);

create index posts_updated_by_index
    on posts (updated_by);

create table if not exists queue_logs
(
    id               bigint unsigned auto_increment comment '主键'
        primary key,
    exchange_name    varchar(32)        not null comment '交换机名称',
    routing_key_name varchar(32)        not null comment '路由名称',
    queue_name       varchar(64)        not null comment '队列名称',
    queue_content    longtext           null comment '队列数据',
    log_content      text               null comment '队列日志',
    produce_status   smallint default 1 null comment '生产状态 1:未生产 2:生产中 3:生产成功 4:生产失败 5:生产重复',
    consume_status   smallint default 1 null comment '消费状态 1:未消费 2:消费中 3:消费成功 4:消费失败 5:消费重复',
    delay_time       int unsigned       not null comment '延迟时间（秒）',
    created_by       bigint             null comment '创建者',
    created_at       timestamp          null comment '创建时间',
    updated_at       timestamp          null comment '更新时间',
    deleted_at       timestamp          null comment '删除时间'
)
    comment '队列日志表';

create table if not exists role_organization
(
    organization_id bigint unsigned not null comment '组织主键',
    role_id         bigint unsigned not null comment '角色主键',
    primary key (organization_id, role_id)
)
    comment '组织与角色关联表';

create table if not exists role_user
(
    user_id bigint unsigned not null comment '用户主键',
    role_id bigint unsigned not null comment '角色主键',
    primary key (user_id, role_id)
)
    comment '用户与角色关联表';

create table if not exists roles
(
    id         bigint unsigned auto_increment
        primary key,
    name       varchar(30)        not null comment '角色名称',
    code       varchar(100)       not null comment '角色代码',
    type       bigint   default 1 null comment '角色类型(1公共角色 2UGC角色)',
    data_scope smallint default 1 null comment '数据范围（1：全部数据权限 2：自定义数据权限 3：本部门数据权限 4：本部门及以下数据权限 5：本人数据权限）',
    status     smallint default 1 null comment '状态 (1正常 2停用)',
    created_by bigint             null comment '创建者',
    updated_by bigint             null comment '更新者',
    remark     varchar(255)       null comment '备注',
    created_at datetime           null,
    updated_at datetime           null,
    deleted_at timestamp          null
);

create index roles_created_by_index
    on roles (created_by);

create index roles_status_index
    on roles (status);

create index roles_updated_by_index
    on roles (updated_by);

create table if not exists upload_files
(
    id           bigint unsigned auto_increment comment '主键'
        primary key,
    storage_mode smallint default 1 null comment '存储模式 (1.本地 2.阿里云OSS 3.FTP 4.内存 5.S3 6.Minio 7.七牛云 8.腾讯云COS)',
    origin_name  varchar(255)       null comment '原文件名',
    object_name  varchar(50)        null comment '新文件名',
    hash         varchar(64)        null comment '文件hash',
    mime_type    varchar(255)       null comment '资源类型',
    storage_path varchar(100)       null comment '存储目录',
    suffix       varchar(10)        null comment '文件后缀',
    size_byte    bigint             null comment '字节数',
    size_info    varchar(50)        null comment '文件大小',
    url          varchar(255)       null comment 'url地址',
    status       smallint default 1 null comment '状态 (1完成 2未完成)',
    created_by   bigint             null comment '创建者',
    updated_by   bigint             null comment '更新者',
    created_at   timestamp          null comment '创建时间',
    updated_at   timestamp          null comment '更新时间',
    deleted_at   timestamp          null comment '删除时间',
    remark       varchar(255)       null comment '备注',
    constraint upload_files_hash_unique
        unique (hash)
)
    comment '上传文件信息表';

create index upload_files_status_index
    on upload_files (status);

create index upload_files_storage_path_index
    on upload_files (storage_path);

create table if not exists users
(
    id         bigint unsigned auto_increment
        primary key,
    username   varchar(20)              not null comment '账号',
    password   varchar(100)             not null comment '密码',
    status     smallint   default 1     null comment '状态 (1正常 2停用)',
    phone      varchar(11)              null comment '手机',
    login_ip   varchar(45)              null comment '最后登陆IP',
    login_time datetime                 null comment '最后登陆时间',
    created_by bigint                   null comment '创建者',
    updated_by bigint                   null comment '更新者',
    remark     varchar(255)             null comment '备注',
    created_at datetime                 null,
    updated_at datetime                 null,
    deleted_at timestamp                null,
    user_type  varchar(3) default '100' null comment '用户类型：(100系统用户)',
    constraint users_username_unique
        unique (username)
);

create index users_created_by_index
    on users (created_by);

create index users_status_index
    on users (status);

create index users_updated_by_index
    on users (updated_by);

