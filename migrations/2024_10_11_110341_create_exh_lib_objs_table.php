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
        Schema::create('exh_lib_objs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title', 100)->comment('标题');
            $table->string('author', 30)->comment('作者');
            $table->string('phone', 15)->comment('手机');
            $table->string('email', 100)->comment('邮箱');
            $table->text('profile')->comment('专区简介');
            $table->integer('save_dir_id')->comment('存储文件夹ID')->nullable();
            $table->string('redirect_url', 511)->comment('跳转地址')->nullable();
            $table->addColumn('smallInteger', 'type', ['default' => 1, 'comment' => '展项类型 (1虚拟展项素材 2实体展项素材 3平台展项素材)'])->index()->nullable();
            $table->addColumn('smallInteger', 'lib_type', ['default' => 1, 'comment' => '大区类型 (1战新 2行业 3主题 4专场)'])->index()->nullable();
            $table->addColumn('smallInteger', 'lib_area_type', ['default' => 0, 'comment' => '子分区分类'])->index()->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 2, 'comment' => '状态 (1正常 2停用)'])->index()->nullable();
            $table->addColumn('smallInteger', 'audit_status', ['default' => 1, 'comment' => '审核 (1审核中 2通过 3拒绝)'])->index()->nullable();
            $table->addColumn('smallInteger', 'sort', ['unsigned' => true, 'default' => 0, 'comment' => '排序'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->index()->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->index()->nullable();
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
        Schema::dropIfExists('exh_lib_objs');
    }
};
