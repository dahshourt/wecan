<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NewWorkFlow;
use App\Models\Status;

echo "=== Finding IOT In progress workflow IDs ===\n";

// First, find the IOT In progress status ID
$iotInProgressStatus = Status::where('status_name', 'IOT In Progress')
    ->where('active', '1')
    ->first();

if (!$iotInProgressStatus) {
    echo "IOT In Progress status not found!\n";
    exit(1);
}

echo "IOT In Progress status ID: {$iotInProgressStatus->id}\n";

// Find workflows that lead to IOT In progress
$workflows = NewWorkFlow::with('workflowstatus')
    ->whereHas('workflowstatus', function($query) use ($iotInProgressStatus) {
        $query->where('to_status_id', $iotInProgressStatus->id);
    })
    ->get();

echo "\nWorkflows that lead to IOT In Progress:\n";
foreach ($workflows as $wf) {
    echo "  Workflow ID: {$wf->id}\n";
    if ($wf->workflowstatus && !$wf->workflowstatus->isEmpty()) {
        $ws = $wf->workflowstatus->first();
        echo "    From Status ID: {$ws->from_status_id}\n";
        echo "    To Status ID: {$ws->to_status_id}\n";
        
        // Get from status name
        $fromStatus = Status::find($ws->from_status_id);
        echo "    From Status Name: " . ($fromStatus ? $fromStatus->status_name : 'Unknown') . "\n";
    }
    echo "\n";
}

echo "=== Done ===\n";
