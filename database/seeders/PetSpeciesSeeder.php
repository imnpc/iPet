<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 宠物物种默认数据填充。
 */
class PetSpeciesSeeder extends Seeder
{
    /**
     * 执行数据填充。
     */
    public function run(): void
    {
        $now = now();

        // 宠物物种默认数据（猫排第一）
        $speciesDefaults = [
            [
                'name' => '猫',
                'icon' => 'heroicon-o-heart',
                'sort_order' => 1,
                'is_enabled' => true,
            ],
            [
                'name' => '狗',
                'icon' => 'heroicon-o-heart',
                'sort_order' => 2,
                'is_enabled' => true,
            ],
            [
                'name' => '兔子',
                'icon' => 'heroicon-o-heart',
                'sort_order' => 3,
                'is_enabled' => true,
            ],
            [
                'name' => '其他',
                'icon' => 'heroicon-o-heart',
                'sort_order' => 99,
                'is_enabled' => true,
            ],
        ];

        foreach ($speciesDefaults as $species) {
            DB::table('pet_species')->updateOrInsert(
                ['name' => $species['name']],
                [...$species, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
