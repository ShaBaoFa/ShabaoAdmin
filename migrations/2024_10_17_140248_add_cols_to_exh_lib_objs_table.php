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
        Schema::table('exh_lib_objs', function (Blueprint $table) {
            // 点赞次数
            $table->unsignedInteger('star_count')->default(0)->comment('点赞次数');
            // 收藏次数
            $table->unsignedInteger('collect_count')->default(0)->comment('收藏次数');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exh_lib_objs', function (Blueprint $table) {
            $table->dropColumn('star_count');
            $table->dropColumn('collect_count');
        });
    }
};
