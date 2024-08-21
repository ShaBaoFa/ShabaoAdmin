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

class CreateDiskFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disk_files', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('云盘表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'name', ['length' => 255, 'comment' => '文件(文件夹)名'])->nullable();
            $table->addColumn('string', 'level', ['length' => 500, 'comment' => '文件(文件夹)路径'])->nullable();
            $table->addColumn('string', 'hash', ['length' => 64, 'comment' => '文件hash'])->nullable();
            $table->addColumn('string', 'suffix', ['length' => 10, 'comment' => '文件后缀'])->nullable();
            $table->addColumn('bigInteger', 'parent_id', ['default' => 0, 'unsigned' => true, 'comment' => '父ID'])->index();
            $table->addColumn('bigInteger', 'size_byte', ['length' => 20, 'comment' => '字节数'])->nullable();
            $table->addColumn('string', 'size_info', ['length' => 50, 'comment' => '文件大小'])->nullable();
            $table->addColumn('smallInteger', 'is_folder', ['default' => 0, 'comment' => '是否文件夹'])->index()->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disk_files');
    }
}
