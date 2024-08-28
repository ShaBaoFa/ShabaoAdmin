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
        Schema::create('disk_file_share_file', function (Blueprint $table) {
            $table->comment('分享接收文件表');
            $table->addColumn('bigInteger', 'share_id', ['unsigned' => true, 'comment' => '分享包主键']);
            $table->addColumn('bigInteger', 'file_id', ['unsigned' => true, 'comment' => '接收分享的文件ID']);
            $table->primary(['share_id', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disk_file_share_file');
    }
};
