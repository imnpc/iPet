<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $speciesMap = [
            ['name' => '狗', 'sort_order' => 1],
            ['name' => '猫', 'sort_order' => 2],
            ['name' => '兔子', 'sort_order' => 3],
            ['name' => '仓鼠', 'sort_order' => 4],
            ['name' => '乌龟', 'sort_order' => 5],
            ['name' => '鹦鹉', 'sort_order' => 6],
            ['name' => '龙猫', 'sort_order' => 7],
            ['name' => '其他', 'sort_order' => 99],
        ];

        $nameMapping = [
            'dog' => '狗',
            'cat' => '猫',
        ];

        foreach ($speciesMap as $species) {
            DB::table('pet_species')->insert($species + ['is_enabled' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        $speciesIds = DB::table('pet_species')->pluck('id', 'name')->toArray();

        Schema::table('pets', function (Blueprint $table) {
            $table->foreignId('pet_species_id')->nullable()->after('species')->constrained('pet_species');
        });

        $pets = DB::table('pets')->get();
        foreach ($pets as $pet) {
            $speciesName = $pet->species;
            if (isset($nameMapping[$speciesName])) {
                $speciesName = $nameMapping[$speciesName];
            }

            $speciesId = $speciesIds[$speciesName] ?? $speciesIds['其他'] ?? null;

            if ($speciesId) {
                DB::table('pets')->where('id', $pet->id)->update(['pet_species_id' => $speciesId]);
            }
        }

        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('species');
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->string('species', 30)->nullable()->after('name');
        });

        $speciesIds = DB::table('pet_species')->pluck('name', 'id')->toArray();
        $pets = DB::table('pets')->get();
        foreach ($pets as $pet) {
            if ($pet->pet_species_id && isset($speciesIds[$pet->pet_species_id])) {
                DB::table('pets')->where('id', $pet->id)->update(['species' => $speciesIds[$pet->pet_species_id]]);
            }
        }

        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['pet_species_id']);
            $table->dropColumn('pet_species_id');
        });

        DB::table('pet_species')->truncate();
    }
};
