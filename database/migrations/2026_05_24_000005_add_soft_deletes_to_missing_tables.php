<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_media', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pet_species', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pet_record_types', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('post_media', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pet_species', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pet_record_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
