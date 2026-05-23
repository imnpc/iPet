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
        Schema::table('pet_records', function (Blueprint $table) {
            // 添加公开/私有字段：默认只能自己查看，选择公开后别人才能看到
            $table->boolean('is_public')->default(false)->comment('是否公开')->after('cost');
            $table->index(['pet_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pet_records', function (Blueprint $table) {
            $table->dropIndex(['pet_id', 'is_public']);
            $table->dropColumn('is_public');
        });
    }
};
