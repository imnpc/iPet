<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 执行迁移。
     */
    public function up(): void
    {
        if (! Schema::hasTable('pet_species') || ! Schema::hasTable('pet_record_types')) {
            return;
        }

        $now = now();

        $speciesDefaults = [
            ['name' => '猫', 'icon' => 'heroicon-o-heart', 'sort_order' => 1, 'is_enabled' => true],
            ['name' => '狗', 'icon' => 'heroicon-o-heart', 'sort_order' => 2, 'is_enabled' => true],
            ['name' => '兔子', 'icon' => 'heroicon-o-heart', 'sort_order' => 3, 'is_enabled' => true],
            ['name' => '其他', 'icon' => 'heroicon-o-heart', 'sort_order' => 99, 'is_enabled' => true],
        ];

        foreach ($speciesDefaults as $species) {
            DB::table('pet_species')->updateOrInsert(
                ['name' => $species['name']],
                [...$species, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $recordTypeDefaults = [
            ['name' => '疫苗', 'slug' => 'vaccine', 'color' => '#10B981', 'sort_order' => 1, 'is_enabled' => true],
            ['name' => '体检', 'slug' => 'checkup', 'color' => '#3B82F6', 'sort_order' => 2, 'is_enabled' => true],
            ['name' => '病历/疾病', 'slug' => 'illness', 'color' => '#EF4444', 'sort_order' => 3, 'is_enabled' => true],
            ['name' => '用药', 'slug' => 'medication', 'color' => '#F59E0B', 'sort_order' => 4, 'is_enabled' => true],
            ['name' => '手术', 'slug' => 'surgery', 'color' => '#8B5CF6', 'sort_order' => 5, 'is_enabled' => true],
            ['name' => '美容护理', 'slug' => 'grooming', 'color' => '#EC4899', 'sort_order' => 6, 'is_enabled' => true],
            ['name' => '其他', 'slug' => 'other', 'color' => '#6B7280', 'sort_order' => 99, 'is_enabled' => true],
        ];

        foreach ($recordTypeDefaults as $recordType) {
            DB::table('pet_record_types')->updateOrInsert(
                ['slug' => $recordType['slug']],
                [...$recordType, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    /**
     * 回滚迁移。
     */
    public function down(): void
    {
        if (! Schema::hasTable('pet_species') || ! Schema::hasTable('pet_record_types')) {
            return;
        }

        DB::table('pet_species')
            ->whereIn('name', ['猫', '狗', '兔子', '其他'])
            ->delete();

        DB::table('pet_record_types')
            ->whereIn('slug', ['vaccine', 'checkup', 'illness', 'medication', 'surgery', 'grooming', 'other'])
            ->delete();
    }
};
