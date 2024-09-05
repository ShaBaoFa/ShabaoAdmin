<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exh_lib_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',10)->comment('专区名称');
            $table->string('icon',30)->comment('专区icon');
            $table->text('profile')->comment('专区简介')->nullable();
            $table->addColumn('smallInteger', 'lib_type', ['default' => 1, 'comment' => '大区类型 (1战新 2行业 3主题 4专场)'])->index()->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->index()->nullable();
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
        Schema::dropIfExists('exh_lib_areas');
    }
};
