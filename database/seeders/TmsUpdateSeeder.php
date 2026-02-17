<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class TmsUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sqlPath = database_path('seeders/sql/tms_update_20260212.sql');

        if (!File::exists($sqlPath)) {
            $this->command->error("SQL file not found at: {$sqlPath}");
            return;
        }

        $sqlContent = File::get($sqlPath);

        // 1. Identify tables to be backed up
        // Look for INSERT INTO `tablename` patterns
        preg_match_all('/INSERT INTO `([^`]+)`/', $sqlContent, $matchesInsert);
        preg_match_all('/UPDATE `([^`]+)`/', $sqlContent, $matchesUpdate);

        $tables = array_unique(array_merge($matchesInsert[1] ?? [], $matchesUpdate[1] ?? []));

        if (empty($tables)) {
            $this->command->info("No tables found to backup.");
        } else {
            $timestamp = date('Y_m_d_His');
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $backupTableName = "{$table}_backup_{$timestamp}";
                    $this->command->info("Backing up table '{$table}' to '{$backupTableName}'...");

                    try {
                        // Create backup table structure and data
                        DB::statement("CREATE TABLE `{$backupTableName}` AS SELECT * FROM `{$table}`");
                    } catch (\Exception $e) {
                        $this->command->error("Failed to backup table '{$table}': " . $e->getMessage());
                    }
                }
            }
        }

        // 2. Execute the SQL content
        $this->command->info("Seeding data from SQL file...");

        try {
            DB::unprepared($sqlContent);
            $this->command->info("Seeding completed successfully.");
        } catch (\Exception $e) {
            $this->command->error("Error executing SQL file: " . $e->getMessage());
        }
    }
}
