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

    // Step 2: Find the most recent status record
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

    // Step 4: Fix the status records - ensure only one is active
    echo PHP_EOL . "=== FIXING ACTIVE STATUSES ===" . PHP_EOL;
    
    // First, set all statuses to completed (active=2) to clean up
    $updatedCount = ChangeRequestStatus::where('cr_id', 31351)
        ->update(['active' => '2']);
    echo "Set " . $updatedCount . " status records to completed (active=2)" . PHP_EOL;

    // Then set the latest one to active (active=1)
    $latestStatus->active = '1';
    $latestStatus->save();
    echo "Set latest status record (ID: " . $latestStatus->id . ") to active=1" . PHP_EOL;

    // Step 5: Verify the fix
    echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
    $activeStatuses = ChangeRequestStatus::where('cr_id', 31351)
        ->where('active', '1')
        ->with(['status'])
        ->get();

    echo "Active statuses count: " . $activeStatuses->count() . PHP_EOL;
    
    foreach ($activeStatuses as $status) {
        echo "  - ID: " . $status->id . 
             " | Status: " . ($status->status ? $status->status->status_name : 'Unknown') .
             " | Active: " . $status->active .
             PHP_EOL;
    }

    // Step 6: Test current status retrieval
    echo PHP_EOL . "=== TESTING CURRENT STATUS RETRIEVAL ===" . PHP_EOL;
    $currentStatus = $cr->getCurrentStatus();
    if ($currentStatus) {
        echo "✓ Current status retrieved successfully" . PHP_EOL;
        echo "  Status ID: " . $currentStatus->new_status_id . PHP_EOL;
        $currentStatusName = Status::find($currentStatus->new_status_id);
        echo "  Status Name: " . ($currentStatusName ? $currentStatusName->status_name : 'Unknown') . PHP_EOL;
    } else {
        echo "⚠️  Could not retrieve current status" . PHP_EOL;
    }

    // Step 7: Test workflow availability
    echo PHP_EOL . "=== TESTING WORKFLOW AVAILABILITY ===" . PHP_EOL;
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
            echo "  From Status: " . $fixDefectStatus->status_name . " (ID: " . $fixDefectStatus->id . ")" . PHP_EOL;
            echo "  To Status: " . $iotInProgressStatus->status_name . " (ID: " . $iotInProgressStatus->id . ")" . PHP_EOL;
        } else {
            echo "⚠️  Workflow from Fix Defect-3rd Parties to IOT In progress not found" . PHP_EOL;
        }
    }

    if ($activeStatuses->count() == 1) {
        echo PHP_EOL . "✅ SUCCESS: CR 31351 has been fixed!" . PHP_EOL;
        echo "✓ Exactly one active status exists" . PHP_EOL;
        echo "✓ Current status should now be retrievable" . PHP_EOL;
        echo "✓ Workflow transitions should work normally" . PHP_EOL;
    } else {
        throw new Exception("Fix verification failed - expected 1 active status, found " . $activeStatuses->count());
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
echo "3. The system should properly identify the current active status" . PHP_EOL;
echo "4. Monitor the application logs to ensure the workflow processes correctly" . PHP_EOL;
