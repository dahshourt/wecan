<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== CR 31351 CURRENT STATUS ===" . PHP_EOL;
$cr = ChangeRequest::find(31351);
if ($cr) {
    echo "CR ID: " . $cr->id . PHP_EOL;
    echo "CR No: " . $cr->cr_no . PHP_EOL;
    echo "Workflow Type ID: " . $cr->workflow_type_id . PHP_EOL;
    
    $currentStatus = $cr->getCurrentStatus();
    if ($currentStatus) {
        echo "Current Status ID: " . $currentStatus->new_status_id . PHP_EOL;
        echo "Current Status Name: " . ($currentStatus->status ? $currentStatus->status->status_name : 'N/A') . PHP_EOL;
        echo "Current Status Active: " . $currentStatus->active . PHP_EOL;
    } else {
        echo "No current status found" . PHP_EOL;
    }
} else {
    echo "CR 31351 not found" . PHP_EOL;
}

echo PHP_EOL . "=== RECENT STATUS HISTORY ===" . PHP_EOL;
$statuses = ChangeRequestStatus::where('cr_id', 31351)
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($statuses as $status) {
    $oldStatus = Status::find($status->old_status_id);
    $newStatus = Status::find($status->new_status_id);
    
    echo "ID: " . $status->id . 
         " | Old: " . ($oldStatus ? $oldStatus->status_name : $status->old_status_id) .
         " -> New: " . ($newStatus ? $newStatus->status_name : $status->new_status_id) .
         " | Active: " . $status->active .
         " | Created: " . $status->created_at .
         PHP_EOL;
}

echo PHP_EOL . "=== WORKFLOW TYPE CHECK ===" . PHP_EOL;
echo "Required Workflow Type for transition: 5" . PHP_EOL;
echo "CR Current Workflow Type: " . ($cr ? $cr->workflow_type_id : 'N/A') . PHP_EOL;

echo PHP_EOL . "=== FIX DEFECT TO IOT WORKFLOW ===" . PHP_EOL;
$workflow = \App\Models\NewWorkFlow::where('from_status_id', 343)
    ->whereHas('workflowstatus', function($q) {
        $q->where('to_status_id', 340);
    })
    ->where('type_id', 5)
    ->first();

if ($workflow) {
    echo "Workflow Found: ID " . $workflow->id . PHP_EOL;
    echo "Workflow Type: " . $workflow->type_id . PHP_EOL;
    echo "Workflow Active: " . $workflow->active . PHP_EOL;
} else {
    echo "No workflow found for this transition with type 5" . PHP_EOL;
}
