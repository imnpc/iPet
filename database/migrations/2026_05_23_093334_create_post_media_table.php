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
        Schema::create('post_media', function (Blueprint $table) {
            $table->comment('动态媒体');
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['image', 'video'])->comment('媒体类型');
            $table->string('disk', 30)->default('oss')->comment('存储磁盘(local/oss)');
            $table->string('path', 500)->comment('存储路径');
            $table->string('thumbnail_path', 500)->nullable()->comment('缩略图/视频封面');
            $table->string('mime_type', 100)->nullable()->comment('MIME类型');
            $table->unsignedBigInteger('size')->nullable()->comment('字节');
            $table->unsignedSmallInteger('width')->nullable()->comment('宽度');
            $table->unsignedSmallInteger('height')->nullable()->comment('高度');
            $table->unsignedSmallInteger('duration')->nullable()->comment('视频时长(秒)');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('排序');

            $table->timestamps();

            $table->index(['post_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_media');
    }
};
