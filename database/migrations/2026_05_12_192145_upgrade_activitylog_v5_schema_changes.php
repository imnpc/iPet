<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = config('activitylog.database_connection');
        $tableName = (string) config('activitylog.table_name', 'activity_log');

        if (! Schema::connection($connection)->hasColumn($tableName, 'attribute_changes')) {
            Schema::connection($connection)->table($tableName, function (Blueprint $table) {
                $table->json('attribute_changes')->nullable()->after('causer_id');
            });
        }

        DB::connection($connection)
            ->table($tableName)
            ->select(['id', 'properties'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($connection, $tableName): void {
                foreach ($rows as $row) {
                    $properties = is_array($row->properties)
                        ? $row->properties
                        : json_decode((string) $row->properties, true);

                    if (! is_array($properties)) {
                        continue;
                    }

                    $attributeChanges = [];

                    if (array_key_exists('attributes', $properties)) {
                        $attributeChanges['attributes'] = $properties['attributes'];
                        unset($properties['attributes']);
                    }

                    if (array_key_exists('old', $properties)) {
                        $attributeChanges['old'] = $properties['old'];
                        unset($properties['old']);
                    }

                    if ($attributeChanges === []) {
                        continue;
                    }

                    DB::connection($connection)
                        ->table($tableName)
                        ->where('id', $row->id)
                        ->update([
                            'attribute_changes' => $attributeChanges,
                            'properties' => $properties === [] ? null : $properties,
                        ]);
                }
            }, 'id');

        if (Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
            Schema::connection($connection)->table($tableName, function (Blueprint $table) {
                $table->dropColumn('batch_uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('activitylog.database_connection');
        $tableName = (string) config('activitylog.table_name', 'activity_log');

        if (! Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
            Schema::connection($connection)->table($tableName, function (Blueprint $table) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            });
        }

        DB::connection($connection)
            ->table($tableName)
            ->select(['id', 'properties', 'attribute_changes'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($connection, $tableName): void {
                foreach ($rows as $row) {
                    $properties = is_array($row->properties)
                        ? $row->properties
                        : json_decode((string) $row->properties, true);
                    $changes = is_array($row->attribute_changes)
                        ? $row->attribute_changes
                        : json_decode((string) $row->attribute_changes, true);

                    if (! is_array($properties)) {
                        $properties = [];
                    }

                    if (is_array($changes)) {
                        if (array_key_exists('attributes', $changes)) {
                            $properties['attributes'] = $changes['attributes'];
                        }

                        if (array_key_exists('old', $changes)) {
                            $properties['old'] = $changes['old'];
                        }
                    }

                    DB::connection($connection)
                        ->table($tableName)
                        ->where('id', $row->id)
                        ->update([
                            'properties' => $properties === [] ? null : $properties,
                        ]);
                }
            }, 'id');

        if (Schema::connection($connection)->hasColumn($tableName, 'attribute_changes')) {
            Schema::connection($connection)->table($tableName, function (Blueprint $table) {
                $table->dropColumn('attribute_changes');
            });
        }
    }
};
