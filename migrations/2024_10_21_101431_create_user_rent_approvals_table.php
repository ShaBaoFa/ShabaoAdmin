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
        Schema::create('user_rent_approvals', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->unsignedInteger('audit_organization_id')->default(0)->comment('审批组织');
            $table->addColumn('bigInteger', 'user_id', ['unsigned' => true, 'comment' => '申请人主键'])->index()->nullable();
            $table->addColumn('string', 'name', ['length' => 50, 'comment' => '申请人名字']);
            $table->addColumn('string', 'reason', ['length' => 255, 'comment' => '申请理由'])->nullable();
            // 应用项目
            $table->addColumn('string', 'project_name', ['length' => 255, 'comment' => '项目名称']);
            // 租用开始日期
            $table->addColumn('dateTime', 'rent_start_at', ['comment' => '租用开始日期']);
            // 租用结束日期
            $table->addColumn('dateTime', 'rent_end_at', ['comment' => '租用结束日期']);
            // 联系人
            $table->addColumn('string', 'contact_name', ['length' => 255, 'comment' => '联系人']);
            // 联系电话
            $table->addColumn('string', 'contact_phone', ['length' => 255, 'comment' => '联系电话']);
            $table->addColumn('bigInteger', 'exh_lib_obj_id', ['unsigned' => true, 'comment' => '展项主键'])->index()->nullable();
            $table->addColumn('string', 'exh_lib_obj_name', ['length' => 50, 'comment' => '展项名字'])->index();
            $table->addColumn('string', 'cover', ['length' => 255, 'comment' => '展项封面']);
            $table->addColumn('string', 'refuse_reason', ['length' => 255, 'comment' => '拒绝理由'])->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->index()->nullable();
            $table->addColumn('smallInteger', 'audit_status', ['default' => 1, 'comment' => '审核 (1审核中 2通过 3拒绝,4取消)'])->index()->nullable();
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
        Schema::dropIfExists('user_rent_approvals');
    }
};
