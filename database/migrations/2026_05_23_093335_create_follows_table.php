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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete()->comment('关注者');
            $table->foreignId('following_id')->constrained('users')->cascadeOnDelete()->comment('被关注者');
            $table->timestamp('created_at')->comment('关注时间');
            $table->unique(['follower_id', 'following_id'], 'follow_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
