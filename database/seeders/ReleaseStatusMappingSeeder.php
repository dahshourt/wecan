<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReleaseStatus;
use App\Models\ReleaseStatusMapping;

class ReleaseStatusMappingSeeder extends Seeder
{
    public function run()
    {
        // Define the mapping based on the user's provided image
        $mappings = [
            // Release Status => [CR Status Names]
            'Planned' => ['Planned'],
            'Development' => ['Development'],
            'ATP Review' => [
                'Update Release Note',
                'Review Release Note',
                'Pending ATP Review -QC',
                'Pending ATP Review-UAT',
                'ATP Review-UAT',
                'ATP Review-QC',
                'Release ATP Updates',
                'Pending Update ATPs',
            ],
            'Vendor Internal Test' => ['Vendor Internal Testing'],
            'IOT' => [
                'Pending IOT TCs Review',
                'IOT TCs Review',
                'Update IOT TCs',
                'IOT In progress',
            ],
            'E2E' => [
                'Pending E2E Scope',
                'Review E2E TCs',
                'Update E2E TCs',
                'E2E Inprogress',
            ],
            'UAT' => [
                'Pending UAT',
                'UAT In Progress',
                'UAT Sign off',
                'Smoke Test Scope',
                'Pending Review Smoke TCs',
                'Review Smoke TCs',
                'Update Smoke TCs',
                'Fix Defect',
                'Request Production Deployment',
            ],
            'Go Live' => ['Go Live'],
            'Smoke Test' => [
                'Smoke Test',
                'Request Investigation',
            ],
            'Complete' => ['Complete', 'Delivered', 'Closed'],
        ];

        foreach ($mappings as $releaseStatusName => $crStatusNames) {
            $releaseStatus = ReleaseStatus::where('name', $releaseStatusName)->first();
            
            if (!$releaseStatus) {
                continue;
            }

            foreach ($crStatusNames as $crStatusName) {
                ReleaseStatusMapping::updateOrCreate(
                    ['cr_status_name' => $crStatusName],
                    ['release_status_id' => $releaseStatus->id]
                );
            }
        }
    }
}
