<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Technical', 'active' => true],
            ['name' => 'Schedule', 'active' => true],
            ['name' => 'Cost', 'active' => true],
            ['name' => 'Quality', 'active' => true],
            ['name' => 'Legal', 'active' => true],
            ['name' => 'Operational', 'active' => true],
        ];

        foreach ($categories as $category) {
            DB::table('risk_categories')->updateOrInsert(
                ['name' => $category['name']],
                array_merge($category, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
