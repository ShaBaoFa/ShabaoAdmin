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

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->addColumn('bigInteger', 'super_admin_id', ['unsigned' => true, 'comment' => '企业超管ID'])->nullable();
            $table->addColumn('bigInteger', 'parent_id', ['default' => 0, 'unsigned' => true, 'comment' => '父ID'])->index();
            $table->addColumn('bigInteger', 'province_region_id', ['default' => 0, 'unsigned' => true, 'comment' => '省级ID'])->index();
            $table->addColumn('bigInteger', 'province_region_name', ['length' => 30, 'comment' => '省级地区名称']);
            $table->addColumn('bigInteger', 'city_region_id', ['default' => 0, 'unsigned' => true, 'comment' => '市级ID'])->index();
            $table->addColumn('bigInteger', 'city_region_name', ['length' => 30, 'comment' => '市级地区名称']);
            $table->addColumn('string', 'level', ['length' => 500, 'comment' => '组级集合']);
            $table->addColumn('string', 'name', ['length' => 30, 'comment' => '组织名称']);
            $table->addColumn('string', 'address', ['length' => 128, 'comment' => '组织地址'])->nullable();
            $table->addColumn('string', 'legal_person', ['length' => 20, 'comment' => '法人'])->nullable();
            $table->addColumn('string', 'phone', ['length' => 255, 'comment' => '联系电话'])->nullable();
            $table->addColumn('string', 'contact', ['length' => 20, 'comment' => '联系人'])->nullable();
            $table->addColumn('string', 'contact_address', ['length' => 255, 'comment' => '联系地址'])->nullable();
            $table->addColumn('string', 'zip_code', ['length' => 255, 'comment' => '邮政编码'])->nullable();
            $table->addColumn('string', 'email', ['length' => 255, 'comment' => '电子邮箱'])->nullable();
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
        Schema::dropIfExists('organizations');
    }
}
