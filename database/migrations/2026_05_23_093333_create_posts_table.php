<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->comment('动态');
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('作者');
            $table->foreignId('pet_id')->nullable()->constrained()->nullOnDelete()->comment('关联宠物');

            $table->text('content')->comment('正文');
            $table->string('location', 200)->nullable()->comment('位置');
            $table->enum('visibility', ['public', 'followers', 'private'])->default('public')->comment('可见性');
            $table->unsignedInteger('like_count')->default(0)->comment('点赞数');
            $table->unsignedInteger('comment_count')->default(0)->comment('评论数');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览数');
            $table->unsignedInteger('share_count')->default(0)->comment('分享数');
            $table->boolean('is_pinned')->default(false)->comment('置顶');
            $table->boolean('allow_comment')->default(true)->comment('允许评论');
            $table->timestamp('published_at')->nullable()->comment('发布时间(NULL=草稿)');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['pet_id', 'created_at']);
            $table->index(['visibility', 'created_at']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
