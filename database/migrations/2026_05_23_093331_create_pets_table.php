<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->comment('宠物');
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('排序');
            $table->string('name', 50)->comment('宠物名称');
            $table->string('species', 30)->comment('物种：狗/猫/兔子/仓鼠/其他');
            $table->string('breed', 100)->nullable()->comment('品种');
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown')->comment('性别');
            $table->date('birthday')->nullable()->comment('生日');
            $table->date('adoption_date')->nullable()->comment('领养/到家日期');
            $table->string('avatar', 500)->nullable()->comment('头像路径');
            $table->json('metadata')->nullable()->comment('扩展字段：毛色、绝育状态、芯片号等');
            $table->boolean('is_default')->default(false)->comment('是否默认宠物');
            $table->enum('status', ['active', 'archived', 'deceased'])->default('active')->comment('状态');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'sort_order']);
            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
