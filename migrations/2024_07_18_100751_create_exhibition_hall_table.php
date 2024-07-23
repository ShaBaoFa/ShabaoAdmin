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

class CreateExhibitionHallTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exhibition_hall', function (Blueprint $table) {
            $table->comment('展馆与展会关联表');
            $table->addColumn('bigInteger', 'exhibition_id', ['unsigned' => true, 'comment' => '展会主键']);
            $table->addColumn('bigInteger', 'hall_id', ['unsigned' => true, 'comment' => '展馆主键']);
            $table->primary(['exhibition_id', 'hall_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibition_hall');
    }
}
