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

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 20)->unique()->comment('账号');
            $table->string('nickname', 20)->nullable()->comment('昵称');
            $table->string('avatar', 255)->nullable()->comment('头像');
            $table->string('post', 255)->nullable()->comment('岗位');
            $table->string('dept', 255)->nullable()->comment('部门');
            $table->string('password', 100)->comment('密码');
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->index()->nullable();
            $table->addColumn('string', 'phone', ['length' => 11, 'comment' => '手机'])->nullable();
            $table->addColumn('ipAddress', 'login_ip', ['comment' => '最后登陆IP'])->nullable();
            $table->datetime('login_time')->nullable()->comment('最后登陆时间');
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable()->index();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable()->index();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->datetimes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
