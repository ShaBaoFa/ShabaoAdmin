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

class CreateDepartmentOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('department_organization', function (Blueprint $table) {
            $table->comment('部门与组织关联表');
            $table->addColumn('bigInteger', 'department_id', ['unsigned' => true, 'comment' => '部门主键']);
            $table->addColumn('bigInteger', 'organization_id', ['unsigned' => true, 'comment' => '组织主键']);
            $table->primary(['department_id', 'organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_organization');
    }
}
