<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 医疗记录类型默认数据填充。
 */
class PetRecordTypeSeeder extends Seeder
{
    /**
     * 执行数据填充。
     */
    public function run(): void
    {
        $now = now();

        // 医疗记录类型默认数据
        $recordTypeDefaults = [
            [
                'name' => '疫苗',
                'slug' => 'vaccine',
                'color' => '#10B981',
                'sort_order' => 1,
                'is_enabled' => true,
            ],
            [
                'name' => '体检',
                'slug' => 'checkup',
                'color' => '#3B82F6',
                'sort_order' => 2,
                'is_enabled' => true,
            ],
            [
                'name' => '病历/疾病',
                'slug' => 'illness',
                'color' => '#EF4444',
                'sort_order' => 3,
                'is_enabled' => true,
            ],
            [
                'name' => '用药',
                'slug' => 'medication',
                'color' => '#F59E0B',
                'sort_order' => 4,
                'is_enabled' => true,
            ],
            [
                'name' => '手术',
                'slug' => 'surgery',
                'color' => '#8B5CF6',
                'sort_order' => 5,
                'is_enabled' => true,
            ],
            [
                'name' => '美容护理',
                'slug' => 'grooming',
                'color' => '#EC4899',
                'sort_order' => 6,
                'is_enabled' => true,
            ],
            [
                'name' => '其他',
                'slug' => 'other',
                'color' => '#6B7280',
                'sort_order' => 99,
                'is_enabled' => true,
            ],
        ];

        foreach ($recordTypeDefaults as $recordType) {
            DB::table('pet_record_types')->updateOrInsert(
                ['slug' => $recordType['slug']],
                [...$recordType, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
