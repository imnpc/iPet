<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $typeMap = [
            ['name' => '疫苗', 'slug' => 'vaccine', 'color' => 'green', 'sort_order' => 1],
            ['name' => '体检', 'slug' => 'checkup', 'color' => 'blue', 'sort_order' => 2],
            ['name' => '病历', 'slug' => 'illness', 'color' => 'red', 'sort_order' => 3],
            ['name' => '用药', 'slug' => 'medication', 'color' => 'yellow', 'sort_order' => 4],
            ['name' => '手术', 'slug' => 'surgery', 'color' => 'purple', 'sort_order' => 5],
            ['name' => '美容', 'slug' => 'grooming', 'color' => 'pink', 'sort_order' => 6],
            ['name' => '其他', 'slug' => 'other', 'color' => 'warm', 'sort_order' => 99],
        ];

        foreach ($typeMap as $type) {
            DB::table('pet_record_types')->insert($type + ['is_enabled' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        $typeIds = DB::table('pet_record_types')->pluck('id', 'slug')->toArray();

        Schema::table('pet_records', function (Blueprint $table) {
            $table->foreignId('pet_record_type_id')->nullable()->after('type')->constrained('pet_record_types');
        });

        $records = DB::table('pet_records')->get();
        foreach ($records as $record) {
            $typeId = $typeIds[$record->type] ?? $typeIds['other'] ?? null;
            if ($typeId) {
                DB::table('pet_records')->where('id', $record->id)->update(['pet_record_type_id' => $typeId]);
            }
        }

        Schema::table('pet_records', function (Blueprint $table) {
            $table->dropIndex('pet_records_pet_id_type_index');
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('pet_records', function (Blueprint $table) {
            $table->enum('type', [
                'vaccine', 'checkup', 'illness', 'medication', 'surgery', 'grooming', 'other',
            ])->nullable()->after('pet_id');
        });

        $typeSlugs = DB::table('pet_record_types')->pluck('slug', 'id')->toArray();
        $records = DB::table('pet_records')->get();
        foreach ($records as $record) {
            if ($record->pet_record_type_id && isset($typeSlugs[$record->pet_record_type_id])) {
                DB::table('pet_records')->where('id', $record->id)->update(['type' => $typeSlugs[$record->pet_record_type_id]]);
            }
        }

        Schema::table('pet_records', function (Blueprint $table) {
            $table->index(['pet_id', 'type']);
        });

        Schema::table('pet_records', function (Blueprint $table) {
            $table->dropForeign(['pet_record_type_id']);
            $table->dropColumn('pet_record_type_id');
        });

        DB::table('pet_record_types')->truncate();
    }
};
