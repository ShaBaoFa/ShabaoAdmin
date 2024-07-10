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

namespace App\Command;

use App\Model\Menu;
use App\Model\User;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\DbConnection\Db;
use Psr\Container\ContainerInterface;

use function Hyperf\Support\env;

#[Command]
class InstallProjectCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('wlfpanda1012:install');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php wlfpanda1012:install" init data');
        $this->setDescription('安装(未实装),数据初始化');
    }

    public function handle(): void
    {
        $this->installProject();
    }

    protected function installProject(): void
    {
        $this->initSuperAdminData();
        $this->initMenuData();
    }

    protected function initMenuData()
    {
        // 清理数据
        Db::table('menus')->truncate();
        Db::table('menu_role')->truncate();

        // 创建菜单
        $data = $this->menu_data();
        foreach ($data as $i => $value) {
            Menu::create($value);
        }
    }

    private function menu_data(): array
    {
        return [
            0 => [
                'id' => 1000,
                'parent_id' => 0,
                'level' => '0',
                'name' => '权限',
                'code' => 'permission',
                'icon' => 'ma-icon-permission',
                'route' => 'permission',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '2',
                'type' => 'M',
                'status' => 1,
                'sort' => 99,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            1 => [
                'id' => 1100,
                'parent_id' => 1000,
                'level' => '0,1000',
                'name' => '用户管理',
                'code' => 'users',
                'icon' => 'ma-icon-user',
                'route' => 'user',
                'component' => 'system/user/index',
                'redirect' => '',
                'is_hidden' => '2',
                'type' => 'M',
                'status' => 1,
                'sort' => 99,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            2 => [
                'id' => 1101,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户列表',
                'code' => 'users:index',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            3 => [
                'id' => 1102,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户回收站列表',
                'code' => 'users:recycle',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            4 => [
                'id' => 1103,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户保存',
                'code' => 'users:save',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            5 => [
                'id' => 1104,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户更新',
                'code' => 'users:update',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            6 => [
                'id' => 1105,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户删除',
                'code' => 'users:delete',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            7 => [
                'id' => 1106,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户读取',
                'code' => 'users:read',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            8 => [
                'id' => 1107,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户恢复',
                'code' => 'users:recovery',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            9 => [
                'id' => 1108,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户真实删除',
                'code' => 'users:realDelete',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            10 => [
                'id' => 1109,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户导入',
                'code' => 'users:import',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            11 => [
                'id' => 1110,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户导出',
                'code' => 'users:export',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            12 => [
                'id' => 1111,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户状态改变',
                'code' => 'users:changeStatus',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            13 => [
                'id' => 1112,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '用户初始化密码',
                'code' => 'users:initUserPassword',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            14 => [
                'id' => 1113,
                'parent_id' => 1100,
                'level' => '0,1000,1100',
                'name' => '更新用户缓存',
                'code' => 'users:cache',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => '1',
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
        ];
    }

    protected function initSuperAdminData()
    {
        // 清理数据
        Db::table('users')->truncate();
        Db::table('roles')->truncate();
        Db::table('role_user')->truncate();

        // 创建超级管理员
        $superAdminId = Db::table('users')->insertGetId([
            'username' => 'superAdmin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'user_type' => '100',
            'phone' => '15911112222',
            'created_by' => 0,
            'updated_by' => 0,
            'status' => User::STATUS_NORMAL,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        // 创建管理员角色
        $superRoleId = Db::table('roles')->insertGetId([
            'name' => '超级管理员（创始人）',
            'code' => 'superAdmin',
            'data_scope' => 0,
            'created_by' => env('SUPER_ADMIN', 0),
            'updated_by' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'remark' => '系统内置角色，不可删除',
        ]);
        Db::table('role_user')->insertGetId([
            'user_id' => $superAdminId,
            'role_id' => $superRoleId,
        ]);
        $envConfig = <<<ENV
SUPER_ADMIN={$superAdminId}
ADMIN_ROLE={$superAdminId}
ENV;
        file_put_contents(BASE_PATH . '/.env', $envConfig, FILE_APPEND);
    }
}
