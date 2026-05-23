<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_species', function (Blueprint $table) {
            $table->comment('宠物物种');
            $table->id();
            $table->string('name', 50)->comment('物种名称');
            $table->string('icon', 255)->nullable()->comment('图标');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('排序');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();

            $table->index('sort_order');
            $table->index('is_enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_species');
    }
};
