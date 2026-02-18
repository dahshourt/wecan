<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NewWorkFlow;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

echo "=== Finding workflow details ===\n";

// Find the IOT In progress status ID
$iotInProgressStatus = Status::where('status_name', 'IOT In Progress')
    ->where('active', '1')
    ->first();

if (!$iotInProgressStatus) {
    echo "IOT In Progress status not found!\n";
    exit(1);
}

echo "IOT In Progress status ID: {$iotInProgressStatus->id}\n";

// Check new_workflow_statuses table directly
$workflowStatuses = DB::table('new_workflow_statuses')
    ->where('to_status_id', $iotInProgressStatus->id)
    ->get();

echo "\nWorkflow statuses that lead to IOT In Progress:\n";
foreach ($workflowStatuses as $ws) {
    echo "  Workflow Status ID: {$ws->id}\n";
    echo "  New Workflow ID: {$ws->new_workflow_id}\n";
    echo "  To Status ID: {$ws->to_status_id}\n";
    echo "\n";
}

// Now find the actual workflows and their from_status
$workflowIds = $workflowStatuses->pluck('new_workflow_id')->toArray();
echo "Checking workflows with IDs: " . implode(', ', $workflowIds) . "\n";

$workflows = DB::table('new_workflow')
    ->whereIn('id', $workflowIds)
    ->get();

echo "\nWorkflows that lead to IOT In Progress:\n";
foreach ($workflows as $wf) {
    echo "  Workflow ID: {$wf->id}\n";
    echo "  From Status ID: {$wf->from_status_id}\n";
    echo "  To Status ID: (see workflow status above)\n";
    
    // Get from status name
    $fromStatus = Status::find($wf->from_status_id);
    echo "  From Status Name: " . ($fromStatus ? $fromStatus->status_name : 'Unknown') . "\n";
    echo "\n";
}

// Also check what review statuses exist
echo "=== Review statuses ===\n";
$reviewStatuses = Status::where('status_name', 'like', '%IOT TCs Review%')
    ->where('active', '1')
    ->get();

foreach ($reviewStatuses as $status) {
    echo "  Status ID: {$status->id}, Name: {$status->status_name}\n";
}

echo "\n=== Done ===\n";
