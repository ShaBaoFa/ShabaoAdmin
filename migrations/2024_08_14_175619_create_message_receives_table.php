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

class CreateMessageReceivesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_receivers', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('队列消息发送接收人表');
            $table->addColumn('bigInteger', 'message_id', ['unsigned' => true, 'comment' => '队列消息主键']);
            $table->addColumn('bigInteger', 'receiver_id', ['unsigned' => true, 'comment' => '接收用户主键']);
            $table->addColumn('smallInteger', 'read_status', ['default' => 1, 'comment' => '已读状态 (1未读 2已读)'])->nullable();
            $table->primary(['message_id', 'receiver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_receives');
    }
}
