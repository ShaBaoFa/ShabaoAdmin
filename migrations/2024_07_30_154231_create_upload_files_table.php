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

class CreateUploadFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upload_files', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('上传文件信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('smallInteger', 'storage_mode', ['default' => 1, 'comment' => '存储模式 (1.本地 2.阿里云OSS 3.FTP 4.内存 5.S3 6.Minio 7.七牛云 8.腾讯云COS)'])->nullable();
            $table->addColumn('string', 'origin_name', ['length' => 255, 'comment' => '原文件名'])->nullable();
            $table->addColumn('string', 'object_name', ['length' => 50, 'comment' => '新文件名'])->nullable();
            $table->addColumn('string', 'hash', ['length' => 64, 'comment' => '文件hash'])->nullable();
            $table->addColumn('string', 'mime_type', ['length' => 255, 'comment' => '资源类型'])->nullable();
            $table->addColumn('string', 'storage_path', ['length' => 100, 'comment' => '存储目录'])->nullable();
            $table->addColumn('string', 'suffix', ['length' => 10, 'comment' => '文件后缀'])->nullable();
            $table->addColumn('bigInteger', 'size_byte', ['length' => 20, 'comment' => '字节数'])->nullable();
            $table->addColumn('string', 'size_info', ['length' => 50, 'comment' => '文件大小'])->nullable();
            $table->addColumn('string', 'url', ['length' => 255, 'comment' => 'url地址'])->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1完成 2未完成)'])->index()->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('storage_path');
            $table->unique('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_files');
    }
}
