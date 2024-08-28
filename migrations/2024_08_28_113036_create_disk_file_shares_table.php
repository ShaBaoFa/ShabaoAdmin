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

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disk_file_shares', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->string('name', 255)->comment('分享包名称');
            $table->unsignedBigInteger('created_by')->comment('创建者ID');
            $table->unsignedBigInteger('updated_by')->comment('更新者ID');
            $table->string('share_link', 16)->unique()->comment('分享链接的唯一标识符');
            $table->addColumn('smallInteger', 'permission', ['default' => 1, 'comment' => '分享权限（例如：1.查看+下载、2.只查看）']);
            $table->string('share_password', 4)->comment('分享密码');
            $table->unsignedInteger('view_count')->default(0)->comment('查看次数');
            $table->unsignedInteger('download_count')->default(0)->comment('下载次数');
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'expires_at', ['precision' => 0, 'comment' => '到期时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            // 添加外键约束
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disk_file_shares');
    }
};
