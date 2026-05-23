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
        Schema::create('likes', function (Blueprint $table) {
            $table->comment('点赞');
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('点赞用户');
            $table->morphs('likeable');
            $table->timestamp('created_at')->comment('点赞时间');
            $table->unique(['user_id', 'likeable_type', 'likeable_id'], 'like_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
