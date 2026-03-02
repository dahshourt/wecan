<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Models\NewWorkFlow;
use App\Models\NewWorkFlowStatuses;

echo "=== CR 31351 DETAILED ANALYSIS ===" . PHP_EOL;

// Get CR details
$cr = ChangeRequest::find(31351);
if (!$cr) {
    echo "CR 31351 not found" . PHP_EOL;
    exit;
}

echo "CR ID: " . $cr->id . PHP_EOL;
echo "CR No: " . $cr->cr_no . PHP_EOL;
echo "Title: " . $cr->title . PHP_EOL;
echo "Current Status ID: " . $cr->status_id . PHP_EOL;

// Get current status name
$currentStatus = Status::find($cr->status_id);
echo "Current Status Name: " . ($currentStatus ? $currentStatus->status_name : 'NULL/NOT SET') . PHP_EOL;
echo "Application ID: " . $cr->application_id . PHP_EOL;
echo "Created: " . $cr->created_at . PHP_EOL;
echo "Updated: " . $cr->updated_at . PHP_EOL;

echo PHP_EOL . "=== STATUS HISTORY ANALYSIS ===" . PHP_EOL;
$statuses = ChangeRequestStatus::where('cr_id', 31351)
    ->orderBy('id', 'desc')
    ->get();

foreach ($statuses as $status) {
    $oldStatus = Status::find($status->old_status_id);
    $newStatus = Status::find($status->new_status_id);
    $oldName = $oldStatus ? $oldStatus->status_name : 'ID:'.$status->old_status_id;
    $newName = $newStatus ? $newStatus->status_name : 'ID:'.$status->new_status_id;
    
    echo "ID: " . $status->id . 
         " | " . $oldName .
         " -> " . $newName .
         " | Active: " . $status->active .
         " | User: " . $status->user_id .
         " | " . $status->created_at .
         PHP_EOL;
}

echo PHP_EOL . "=== ACTIVE STATUSES CHECK ===" . PHP_EOL;
$activeStatuses = ChangeRequestStatus::where('cr_id', 31351)
    ->where('active', '1')
    ->get();

echo "Active statuses count: " . $activeStatuses->count() . PHP_EOL;
foreach ($activeStatuses as $status) {
    $oldStatus = Status::find($status->old_status_id);
    $newStatus = Status::find($status->new_status_id);
    $oldName = $oldStatus ? $oldStatus->status_name : 'ID:'.$status->old_status_id;
    $newName = $newStatus ? $newStatus->status_name : 'ID:'.$status->new_status_id;
    
    echo "  - " . $oldName . " -> " . $newName . " (ID: " . $status->id . ")" . PHP_EOL;
}

echo PHP_EOL . "=== WORKFLOW AVAILABILITY CHECK ===" . PHP_EOL;

// Check if there's a workflow from Fix Defect-3rd Parties to IOT In progress
$fixDefectStatus = Status::where('status_name', 'Fix Defect-3rd Parties')->first();
$iotInProgressStatus = Status::where('status_name', 'IOT In progress')->first();

if ($fixDefectStatus && $iotInProgressStatus) {
    echo "Fix Defect-3rd Parties Status ID: " . $fixDefectStatus->id . PHP_EOL;
    echo "IOT In progress Status ID: " . $iotInProgressStatus->id . PHP_EOL;
    
    $workflow = NewWorkFlow::where('from_status_id', $fixDefectStatus->id)
        ->whereHas('workflowstatus', function ($query) use ($iotInProgressStatus) {
            $query->where('to_status_id', $iotInProgressStatus->id);
        })
        ->with(['workflowstatus'])
        ->first();
    
    if ($workflow) {
        echo "Workflow FOUND: ID " . $workflow->id . PHP_EOL;
        echo "Workflow Name: " . $workflow->workflow_name . PHP_EOL;
        echo "Active: " . $workflow->active . PHP_EOL;
        
        foreach ($workflow->workflowstatus as $ws) {
            echo "  - To Status ID: " . $ws->to_status_id . 
                 " (Name: " . ($ws->toStatus ? $ws->toStatus->status_name : 'N/A') . ")" . PHP_EOL;
        }
    } else {
        echo "NO WORKFLOW FOUND from Fix Defect-3rd Parties to IOT In progress" . PHP_EOL;
    }
} else {
    echo "Status IDs not found for comparison" . PHP_EOL;
}

echo PHP_EOL . "=== POTENTIAL ISSUES ===" . PHP_EOL;

// Check for multiple active statuses
if ($activeStatuses->count() > 1) {
    echo "⚠️  ISSUE: Multiple active statuses found (" . $activeStatuses->count() . ")" . PHP_EOL;
} elseif ($activeStatuses->count() == 0) {
    echo "⚠️  ISSUE: No active statuses found" . PHP_EOL;
} else {
    echo "✓ OK: Exactly one active status found" . PHP_EOL;
}

// Check if CR status matches active status
if ($activeStatuses->count() == 1) {
    $activeStatus = $activeStatuses->first();
    if ($cr->status_id != $activeStatus->new_status_id) {
        echo "⚠️  ISSUE: CR status_id (" . $cr->status_id . 
             ") does not match active status new_status_id (" . $activeStatus->new_status_id . ")" . PHP_EOL;
    } else {
        echo "✓ OK: CR status_id matches active status" . PHP_EOL;
    }
}

// Check for null current status on CR
if (empty($cr->status_id)) {
    echo "⚠️  CRITICAL ISSUE: CR has NULL/empty status_id" . PHP_EOL;
}
