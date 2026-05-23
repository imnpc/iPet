<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->comment('评论');
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('评论者');
            $table->foreignId('post_id')->constrained()->cascadeOnDelete()->comment('关联动态');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete()->comment('父评论');
            $table->text('content')->comment('内容');
            $table->unsignedInteger('like_count')->default(0)->comment('点赞数');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['post_id', 'created_at']);
            $table->index('parent_id');
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
