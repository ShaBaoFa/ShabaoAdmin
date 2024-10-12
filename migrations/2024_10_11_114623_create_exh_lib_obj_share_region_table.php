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
        Schema::create('exh_lib_obj_share_region', function (Blueprint $table) {
            $table->comment('展项共享区域表');
            $table->addColumn('bigInteger', 'exh_lib_obj_id', ['unsigned' => true, 'comment' => '展项主键']);
            $table->addColumn('bigInteger', 'share_region_id', ['unsigned' => true, 'comment' => '地区ID']);
            $table->primary(['exh_lib_obj_id', 'share_region_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exh_lib_obj_share_region');
    }
};
