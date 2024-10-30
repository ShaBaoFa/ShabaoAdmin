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
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('评论ID');
            // parent_id
            $table->addColumn('bigInteger', 'parent_id', ['default' => 0, 'comment' => '父级ID'])->index()->nullable();
            // star_count
            $table->addColumn('smallInteger', 'star_count', ['default' => 0, 'comment' => '点赞数'])->nullable();
            // body
            $table->text('body')->comment('评论内容');
            // commentable_id
            $table->bigInteger('commentable_id')->comment('评论对象ID');
            // commentable_type
            $table->string('commentable_type', 255)->comment('评论对象类型');
            // sent_to
            $table->addColumn('bigInteger', 'sent_to', ['comment' => '收件人'])->index()->nullable();
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
        Schema::dropIfExists('comments');
    }
};
