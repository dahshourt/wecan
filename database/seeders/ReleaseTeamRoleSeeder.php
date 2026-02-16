<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReleaseTeamRole;

class ReleaseTeamRoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['RTM', 'SA', 'UI-UX', 'QC', 'UAT'];

        foreach ($roles as $role) {
            ReleaseTeamRole::firstOrCreate(['name' => $role]);
        }
    }
}
