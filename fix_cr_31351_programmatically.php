<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

echo "=== FIXING CR 31351 ISSUES ===" . PHP_EOL;

try {
    DB::beginTransaction();

    // Step 1: Get CR details
    $cr = ChangeRequest::find(31351);
    if (!$cr) {
        throw new Exception("CR 31351 not found");
    }

    echo "Found CR 31351: " . $cr->cr_no . " - " . $cr->title . PHP_EOL;
    echo "Current status_id: " . ($cr->status_id ?? 'NULL') . PHP_EOL;

    // Step 2: Find the most recent status that should be active
    $latestStatus = ChangeRequestStatus::where('cr_id', 31351)
        ->orderBy('id', 'desc')
        ->first();

    if (!$latestStatus) {
        throw new Exception("No status records found for CR 31351");
    }

    echo "Latest status record ID: " . $latestStatus->id . PHP_EOL;
    echo "Latest status: " . $latestStatus->old_status_id . " -> " . $latestStatus->new_status_id . PHP_EOL;
    echo "Latest active flag: " . $latestStatus->active . PHP_EOL;

    // Step 3: Get the status name for verification
    $statusName = Status::find($latestStatus->new_status_id);
    echo "Status name: " . ($statusName ? $statusName->status_name : 'Unknown') . PHP_EOL;

    // Step 4: Fix the main CR record
    $cr->status_id = $latestStatus->new_status_id;
    $cr->updated_at = now();
    $cr->save();

    echo "✓ Updated CR status_id to: " . $cr->status_id . PHP_EOL;

    // Step 5: Ensure only this status is active
    // First, set all statuses to inactive/completed
    ChangeRequestStatus::where('cr_id', 31351)
        ->update(['active' => '2']);

    // Then set the latest one to active
    $latestStatus->active = '1';
    $latestStatus->save();

    echo "✓ Set latest status record to active=1" . PHP_EOL;

    // Step 6: Verify the fix
    $updatedCr = ChangeRequest::find(31351);
    $activeStatuses = ChangeRequestStatus::where('cr_id', 31351)
        ->where('active', '1')
        ->count();

    echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
    echo "CR status_id: " . $updatedCr->status_id . PHP_EOL;
    echo "Active statuses count: " . $activeStatuses . PHP_EOL;

    if ($updatedCr->status_id == $latestStatus->new_status_id && $activeStatuses == 1) {
        echo "✅ SUCCESS: CR 31351 has been fixed!" . PHP_EOL;
        
        // Test workflow availability
        $fixDefectStatus = Status::where('status_name', 'Fix Defect-3rd Parties')->first();
        $iotInProgressStatus = Status::where('status_name', 'IOT In progress')->first();
        
        if ($fixDefectStatus && $iotInProgressStatus) {
            $workflow = \App\Models\NewWorkFlow::where('from_status_id', $fixDefectStatus->id)
                ->whereHas('workflowstatus', function ($query) use ($iotInProgressStatus) {
                    $query->where('to_status_id', $iotInProgressStatus->id);
                })
                ->first();
            
            if ($workflow) {
                echo "✓ Workflow from Fix Defect-3rd Parties to IOT In progress is available" . PHP_EOL;
                echo "  Workflow ID: " . $workflow->id . PHP_EOL;
            } else {
                echo "⚠️  Workflow from Fix Defect-3rd Parties to IOT In progress not found" . PHP_EOL;
            }
        }
        
    } else {
        throw new Exception("Fix verification failed");
    }

    DB::commit();
    echo PHP_EOL . "🎉 CR 31351 fix completed successfully!" . PHP_EOL;

} catch (Exception $e) {
    DB::rollback();
    echo "❌ ERROR: " . $e->getMessage() . PHP_EOL;
    echo "Fix failed, changes have been rolled back." . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "=== NEXT STEPS ===" . PHP_EOL;
echo "1. Try transitioning CR 31351 from 'Fix Defect-3rd Parties' to 'IOT In progress'" . PHP_EOL;
echo "2. The transition should now create a new row in change_request_statuses table" . PHP_EOL;
echo "3. Monitor the logs to ensure the workflow processes correctly" . PHP_EOL;
