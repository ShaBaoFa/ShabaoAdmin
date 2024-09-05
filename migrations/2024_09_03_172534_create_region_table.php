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
        Schema::create('region', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('integer', 'parent_id', ['length' => 11, 'default' => 0, 'comment' => '父级'])->index();
            $table->addColumn('boolean', 'level', ['length' => 1, 'default' => 1, 'comment' => '等级']);
            $table->addColumn('string', 'name', ['length' => 50, 'default' => '', 'comment' => '名称'])->index();
            $table->addColumn('string', 'initial', ['length' => 50, 'default' => '', 'comment' => '首字母'])->index();
            $table->addColumn('string', 'pinyin', ['length' => 255, 'default' => '', 'comment' => '拼音'])->index();
            $table->addColumn('string', 'citycode', ['length' => 10, 'default' => '', 'comment' => '城市编码']);
            $table->addColumn('string', 'adcode', ['length' => 10, 'default' => '', 'comment' => '区域编码']);
            $table->addColumn('string', 'lng_lat', ['length' => 30, 'default' => '', 'comment' => '中心经纬度']);
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->index(['name', 'initial', 'pinyin']);
        });
    }

    /**
     * 插入数据.
     */

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region');
    }
};
