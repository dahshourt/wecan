<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\NewWorkFlow;
use App\Models\Status;

echo "=== DEBUGGING STATUS CREATION PROCESS ===" . PHP_EOL;

// Simulate the exact same transition that should happen
$cr = ChangeRequest::find(31351);
$workflow = NewWorkFlow::with('workflowstatus')->find(9103);

echo "CR ID: " . $cr->id . PHP_EOL;
echo "Workflow ID: " . $workflow->id . PHP_EOL;
echo "Workflow Statuses Count: " . $workflow->workflowstatus->count() . PHP_EOL;

foreach ($workflow->workflowstatus as $ws) {
    echo "  Workflow Status: To Status ID " . $ws->to_status_id . PHP_EOL;
    
    $toStatus = Status::find($ws->to_status_id);
    echo "  Status Name: " . ($toStatus ? $toStatus->status_name : 'N/A') . PHP_EOL;
    
    // Check if this status should be skipped ( PromoWorkflowStrategy logic )
    $shouldSkip = $cr->design_duration == '0'
        && $ws->to_status_id == 40
        && 343 == 74; // old_status_id is 343 (Fix Defect-3rd Parties)
    
    echo "  Should Skip: " . ($shouldSkip ? 'YES' : 'NO') . PHP_EOL;
    
    if (!$shouldSkip) {
        echo "  -> This status should be created!" . PHP_EOL;
        
        // Check if there's already a recent status with the same to_status_id
        $existingStatus = ChangeRequestStatus::where('cr_id', 31351)
            ->where('new_status_id', $ws->to_status_id)
            ->where('created_at', '>', now()->subMinutes(5))
            ->first();
            
        if ($existingStatus) {
            echo "  -> Found existing recent status: ID " . $existingStatus->id . PHP_EOL;
        } else {
            echo "  -> No existing recent status found - should create new one" . PHP_EOL;
        }
    }
    
    echo PHP_EOL;
}

echo "=== CHECKING CURRENT ACTIVE STATUSES ===" . PHP_EOL;
$activeStatuses = ChangeRequestStatus::where('cr_id', 31351)
    ->where('active', '1')
    ->get();

echo "Active statuses count: " . $activeStatuses->count() . PHP_EOL;
foreach ($activeStatuses as $as) {
    $statusName = Status::find($as->new_status_id);
    echo "  ID: " . $as->id . " | Status: " . ($statusName ? $statusName->status_name : 'N/A') . PHP_EOL;
}
