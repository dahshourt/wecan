<?php

namespace App\Services;

use App\Models\Release;
use App\Models\ReleaseStatus;
use App\Models\ReleaseStatusMapping;
use App\Models\Change_request;

class ReleaseStatusService
{
    // Calculate and update the release status based on its CRs
    // The release status will be the LOWEST (least progressed) among all its CRs
    public function calculateAndUpdateStatus(Release $release): void
    {
        // Get all CRs for this release
        $crs = Change_request::where('release_name', $release->id)->get();

        if ($crs->isEmpty()) {
            // No CRs - set to first status (Planned)
            $firstStatus = ReleaseStatus::orderBy('display_order')->first();
            if ($firstStatus) {
                $release->release_status_id = $firstStatus->id;
                $release->save();
            }
            return;
        }

        $lowestOrder = PHP_INT_MAX;
        $lowestStatusId = null;

        foreach ($crs as $cr) {
            // Get the CR's current status name
            $crStatus = $cr->currentStatusRel->status ?? null;
            if (!$crStatus) {
                continue;
            }

            $crStatusName = $crStatus->status_name;

            // Find the mapping for this CR status
            $mapping = ReleaseStatusMapping::where('cr_status_name', $crStatusName)->first();
            
            if ($mapping && $mapping->releaseStatus) {
                $releaseStatus = $mapping->releaseStatus;
                
                if ($releaseStatus->display_order < $lowestOrder) {
                    $lowestOrder = $releaseStatus->display_order;
                    $lowestStatusId = $releaseStatus->id;
                }
            }
        }

        // Update release status if we found one
        if ($lowestStatusId !== null) {
            $release->release_status_id = $lowestStatusId;
            $release->save();
        }
    }

    // Recalculate status for a release by ID
    public function recalculateForRelease(int $releaseId): void
    {
        $release = Release::find($releaseId);
        if ($release) {
            $this->calculateAndUpdateStatus($release);
        }
    }

    
    // Recalculate status when a CR's release assignment changes
     
    public function handleCrReleaseChanged(?int $oldReleaseId, ?int $newReleaseId): void
    {
        // Recalculate old release if CR was removed
        if ($oldReleaseId) {
            $this->recalculateForRelease($oldReleaseId);
        }

        // Recalculate new release if CR was added
        if ($newReleaseId) {
            $this->recalculateForRelease($newReleaseId);
        }
    }

    // Recalculate status when a CR's status changes
    public function handleCrStatusChanged(Change_request $cr): void
    {
        if ($cr->release_name) {
            $this->recalculateForRelease($cr->release_name);
        }
    }
}
