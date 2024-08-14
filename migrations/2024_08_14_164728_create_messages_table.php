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

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('消息中心表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'content_type', ['unsigned' => true, 'comment' => '内容类型'])->nullable();
            $table->addColumn('string', 'title', ['length' => 255, 'comment' => '消息标题'])->nullable();
            $table->addColumn('bigInteger', 'send_by', ['unsigned' => true, 'comment' => '发送人'])->nullable();
            $table->addColumn('bigInteger', 'receive_by', ['unsigned' => true, 'comment' => '接受人(私信需填)'])->nullable();
            $table->addColumn('longtext', 'content', ['comment' => '消息内容'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index(['content_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
}
