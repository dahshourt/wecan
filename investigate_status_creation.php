<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Change_request as ChangeRequest;
use App\Models\Status;
use App\Models\NewWorkFlow;
use Illuminate\Support\Facades\DB;

echo "=== INVESTIGATING STATUS CREATION ISSUE ===" . PHP_EOL;

$crId = 31351;
$oldStatusId = 343; // Fix Defect-3rd Parties
$newStatusId = 340; // IOT In progress

echo "CR ID: " . $crId . PHP_EOL;
echo "Old Status ID: " . $oldStatusId . " (Fix Defect-3rd Parties)" . PHP_EOL;
echo "New Status ID: " . $newStatusId . " (IOT In progress)" . PHP_EOL;
echo PHP_EOL;

// 1. Check current CR status
echo "=== 1. CURRENT CR STATUS ===" . PHP_EOL;
$cr = ChangeRequest::find($crId);
if (!$cr) {
    echo "❌ CR not found!" . PHP_EOL;
    exit(1);
}

echo "CR Number: " . $cr->cr_no . PHP_EOL;
echo "CR Title: " . $cr->cr_title . PHP_EOL;
echo "Workflow Type ID: " . $cr->workflow_type_id . PHP_EOL;

$currentStatus = $cr->getCurrentStatus();
if ($currentStatus) {
    echo "Current Status: " . ($currentStatus->status ? $currentStatus->status->status_name : 'N/A') . PHP_EOL;
    echo "Current Status ID: " . $currentStatus->new_status_id . PHP_EOL;
    echo "Current Active: " . $currentStatus->active . PHP_EOL;
    echo "Current Status Record ID: " . $currentStatus->id . PHP_EOL;
} else {
    echo "❌ No current status found!" . PHP_EOL;
}

// 2. Check workflow
echo PHP_EOL . "=== 2. WORKFLOW ANALYSIS ===" . PHP_EOL;
$workflow = NewWorkFlow::where('from_status_id', $oldStatusId)
    ->where('type_id', $cr->workflow_type_id)
    ->where('active', '1')
    ->first();

if ($workflow) {
    echo "✅ Workflow found!" . PHP_EOL;
    echo "Workflow ID: " . $workflow->id . PHP_EOL;
    echo "From Status: " . $workflow->from_status_id . PHP_EOL;
    echo "To Status Label: " . $workflow->to_status_label . PHP_EOL;
    echo "Type ID: " . $workflow->type_id . PHP_EOL;
    echo "Active: " . $workflow->active . PHP_EOL;
    
    // Check workflow statuses
    echo "Workflow Statuses: " . $workflow->workflowstatus->count() . PHP_EOL;
    foreach ($workflow->workflowstatus as $index => $ws) {
        echo "  Status " . ($index + 1) . ": ID " . $ws->to_status_id . " - Dependency IDs: " . ($ws->dependency_ids ? json_encode($ws->dependency_ids) : 'none') . PHP_EOL;
    }
} else {
    echo "❌ Workflow not found!" . PHP_EOL;
    
    // Show available workflows from old status
    $availableWorkflows = NewWorkFlow::where('from_status_id', $oldStatusId)
        ->where('type_id', $cr->workflow_type_id)
        ->get();
    
    echo "Available workflows from status " . $oldStatusId . ":" . PHP_EOL;
    foreach ($availableWorkflows as $wf) {
        echo "  - To: " . $wf->to_status_label . " - Workflow ID: " . $wf->id . PHP_EOL;
    }
}

// 3. Check recent status changes
echo PHP_EOL . "=== 3. RECENT STATUS CHANGES ===" . PHP_EOL;
$recentChanges = ChangeRequestStatus::where('cr_id', $crId)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

echo "Recent status changes:" . PHP_EOL;
foreach ($recentChanges as $change) {
    $oldStatus = Status::find($change->old_status_id);
    $newStatus = Status::find($change->new_status_id);
    echo "  ID: " . $change->id . 
         " | " . ($oldStatus ? $oldStatus->status_name : 'N/A') . 
         " → " . ($newStatus ? $newStatus->status_name : 'N/A') . 
         " | Active: " . $change->active .
         " | Created: " . $change->created_at .
         PHP_EOL;
}

// 4. Check for validation issues
echo PHP_EOL . "=== 4. VALIDATION CHECKS ===" . PHP_EOL;

// Check if there are any active statuses
$activeCount = ChangeRequestStatus::where('cr_id', $crId)
    ->where('active', '1')
    ->count();

echo "Active status count: " . $activeCount . PHP_EOL;

if ($activeCount == 0) {
    echo "⚠️  WARNING: No active statuses found. This might be the issue!" . PHP_EOL;
    echo "The system might be failing because there's no active status to transition from." . PHP_EOL;
}

// Check if the target status already exists
$existingTarget = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', $newStatusId)
    ->orderBy('created_at', 'desc')
    ->first();

if ($existingTarget) {
    echo "⚠️  WARNING: Target status already exists!" . PHP_EOL;
    echo "  Record ID: " . $existingTarget->id . PHP_EOL;
    echo "  Active: " . $existingTarget->active . PHP_EOL;
    echo "  Created: " . $existingTarget->created_at . PHP_EOL;
}

// 5. Simulate the transition process
echo PHP_EOL . "=== 5. SIMULATE TRANSITION PROCESS ===" . PHP_EOL;

try {
    // Start transaction like the real system
    DB::beginTransaction();
    
    echo "Starting transaction..." . PHP_EOL;
    
    // Find current active status
    $currentActive = ChangeRequestStatus::where('cr_id', $crId)
        ->where('active', '1')
        ->first();
    
    if ($currentActive) {
        echo "Found current active status: ID " . $currentActive->id . PHP_EOL;
        
        // Deactivate it (like the real system does)
        $currentActive->update(['active' => '2']);
        echo "Deactivated current status" . PHP_EOL;
    } else {
        echo "❌ No current active status found to deactivate!" . PHP_EOL;
        DB::rollBack();
        exit(1);
    }
    
    // Try to create the new status
    echo "Attempting to create new status..." . PHP_EOL;
    
    $newStatusRecord = new ChangeRequestStatus();
    $newStatusRecord->cr_id = $crId;
    $newStatusRecord->old_status_id = $oldStatusId;
    $newStatusRecord->new_status_id = $newStatusId;
    $newStatusRecord->group_id = null;
    $newStatusRecord->reference_group_id = null;
    $newStatusRecord->previous_group_id = null;
    $newStatusRecord->current_group_id = null;
    $newStatusRecord->user_id = 365;
    $newStatusRecord->sla = 0;
    $newStatusRecord->sla_dif = 0;
    $newStatusRecord->active = '1';
    $newStatusRecord->assignment_user_id = null;
    $newStatusRecord->created_at = now();
    $newStatusRecord->updated_at = null;
    
    $newStatusRecord->save();
    
    echo "✅ New status created successfully! ID: " . $newStatusRecord->id . PHP_EOL;
    
    // Roll back the simulation (we don't want to actually change it)
    DB::rollBack();
    echo "Simulation completed and rolled back." . PHP_EOL;
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Simulation failed: " . $e->getMessage() . PHP_EOL;
    echo "This might be why the interface transition fails!" . PHP_EOL;
}

echo PHP_EOL . "=== INVESTIGATION COMPLETE ===" . PHP_EOL;
echo "If the simulation succeeded but the interface fails, the issue is likely in:" . PHP_EOL;
echo "1. Validation logic in ChangeRequestStatusValidator" . PHP_EOL;
echo "2. Workflow dependency checks" . PHP_EOL;
echo "3. Permission or user context issues" . PHP_EOL;
echo "4. Transaction handling differences between CLI and web" . PHP_EOL;
