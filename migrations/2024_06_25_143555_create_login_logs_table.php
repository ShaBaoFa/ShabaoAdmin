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
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->comment('登录日志表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'username', ['length' => 20, 'comment' => '用户名']);
            $table->addColumn('ipAddress', 'ip', ['comment' => '登录IP地址'])->nullable();
            $table->addColumn('string', 'ip_location', ['length' => 255, 'comment' => 'IP所属地'])->nullable();
            $table->addColumn('string', 'os', ['length' => 50, 'comment' => '操作系统'])->nullable();
            $table->addColumn('string', 'browser', ['length' => 50, 'comment' => '浏览器'])->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '登录状态 (1成功 2失败)']);
            $table->addColumn('string', 'message', ['length' => 50, 'comment' => '提示消息'])->nullable();
            $table->addColumn('timestamp', 'login_time', ['comment' => '登录时间']);
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
}
