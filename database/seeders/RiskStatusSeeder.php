<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Open', 'color' => 'primary', 'active' => true],
            ['name' => 'In Progress', 'color' => 'warning', 'active' => true],
            ['name' => 'Mitigated', 'color' => 'info', 'active' => true],
            ['name' => 'Closed', 'color' => 'success', 'active' => true],
        ];

        foreach ($statuses as $status) {
            DB::table('risk_statuses')->updateOrInsert(
                ['name' => $status['name']],
                array_merge($status, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
