<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NewWorkFlow;
use App\Models\Status;

echo "=== Checking IOT Workflow Transitions ===\n";

// Check workflows from IOT TCs Review QC (ID: 338)
echo "\nWorkflows from IOT TCs Review QC (ID: 338):\n";
$workflowsFromQc = NewWorkFlow::where('from_status_id', 338)->with('workflowstatus')->get();

foreach ($workflowsFromQc as $workflow) {
    echo "  Workflow ID: {$workflow->id}\n";
    foreach ($workflow->workflowstatus as $ws) {
        $toStatus = Status::find($ws->to_status_id);
        echo "    -> {$toStatus->status_name} (ID: {$ws->to_status_id})\n";
    }
}

// Check workflows from IOT TCs Review vendor (ID: 339)
echo "\nWorkflows from IOT TCs Review vendor (ID: 339):\n";
$workflowsFromVendor = NewWorkFlow::where('from_status_id', 339)->with('workflowstatus')->get();

foreach ($workflowsFromVendor as $workflow) {
    echo "  Workflow ID: {$workflow->id}\n";
    foreach ($workflow->workflowstatus as $ws) {
        $toStatus = Status::find($ws->to_status_id);
        echo "    -> {$toStatus->status_name} (ID: {$ws->to_status_id})\n";
    }
}

echo "\n=== Checking if IOT In Progress (ID: 340) is reachable ===\n";
$iotInProgressStatus = Status::find(340);
echo "IOT In Progress status: " . ($iotInProgressStatus ? $iotInProgressStatus->status_name : 'NOT FOUND') . "\n";

// Check if there are any workflows that lead to IOT In Progress
echo "\nWorkflows that lead to IOT In Progress (ID: 340):\n";
$workflowsToIotInProgress = NewWorkFlow::whereHas('workflowstatus', function($query) {
    $query->where('to_status_id', 340);
})->with(['workflowstatus' => function($query) {
    $query->where('to_status_id', 340);
}])->get();

foreach ($workflowsToIotInProgress as $workflow) {
    $fromStatus = Status::find($workflow->from_status_id);
    echo "  From: " . ($fromStatus ? $fromStatus->status_name : 'N/A') . " (ID: {$workflow->from_status_id})\n";
    echo "  Workflow ID: {$workflow->id}\n";
}
