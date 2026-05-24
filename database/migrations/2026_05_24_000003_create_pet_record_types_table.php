<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_record_types', function (Blueprint $table) {
            $table->comment('医疗记录类型');
            $table->id();
            $table->string('name', 50)->comment('类型名称');
            $table->string('slug', 30)->comment('标识');
            $table->string('color', 20)->nullable()->comment('颜色代码');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('排序');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();

            $table->index('sort_order');
            $table->index('is_enabled');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('pet_record_types');
    }
};
