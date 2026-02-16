<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReleaseStatus;

class ReleaseStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Planned', 'display_order' => 1, 'color' => '#FFA500'],
            ['name' => 'Development', 'display_order' => 2, 'color' => '#FFA500'],
            ['name' => 'ATP Review', 'display_order' => 3, 'color' => '#FFA500'],
            ['name' => 'Vendor Internal Test', 'display_order' => 4, 'color' => '#FFA500'],
            ['name' => 'IOT', 'display_order' => 5, 'color' => '#28A745'],
            ['name' => 'E2E', 'display_order' => 6, 'color' => '#28A745'],
            ['name' => 'UAT', 'display_order' => 7, 'color' => '#28A745'],
            ['name' => 'Go Live', 'display_order' => 8, 'color' => '#28A745'],
            ['name' => 'Smoke Test', 'display_order' => 9, 'color' => '#28A745'],
            ['name' => 'Complete', 'display_order' => 10, 'color' => '#28A745'],
        ];

        foreach ($statuses as $status) {
            ReleaseStatus::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
