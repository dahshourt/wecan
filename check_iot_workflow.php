<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking IOT workflow configurations:\n\n";

// Status IDs from our previous check
$pendingIotTcReviewQc = 336;
$pendingIotTcReviewSa = 337;
$iotTcReviewQc = 338;
$iotTcReviewVendor = 339;
$iotInProgress = 340;

echo "Status IDs:\n";
echo "Pending IOT TCs Review QC: $pendingIotTcReviewQc\n";
echo "Pending IOT TCs Review SA: $pendingIotTcReviewSa\n";
echo "IOT TCs Review QC: $iotTcReviewQc\n";
echo "IOT TCs Review vendor: $iotTcReviewVendor\n";
echo "IOT In progress: $iotInProgress\n\n";

// Check workflows from pending statuses to review statuses
echo "=== Workflows from Pending IOT TCs Review QC (336) ===\n";
$workflowsFromQc = \App\Models\NewWorkFlow::with('workflowstatus')
    ->where('from_status_id', $pendingIotTcReviewQc)
    ->get();

foreach ($workflowsFromQc as $workflow) {
    echo "Workflow ID: {$workflow->id}, Same Time: {$workflow->same_time}\n";
    foreach ($workflow->workflowstatus as $status) {
        $toStatus = \App\Models\Status::find($status->to_status_id);
        echo "  -> To: {$status->to_status_id} ({$toStatus->status_name})\n";
    }
    echo "\n";
}

echo "=== Workflows from Pending IOT TCs Review SA (337) ===\n";
$workflowsFromSa = \App\Models\NewWorkFlow::with('workflowstatus')
    ->where('from_status_id', $pendingIotTcReviewSa)
    ->get();

foreach ($workflowsFromSa as $workflow) {
    echo "Workflow ID: {$workflow->id}, Same Time: {$workflow->same_time}\n";
    foreach ($workflow->workflowstatus as $status) {
        $toStatus = \App\Models\Status::find($status->to_status_id);
        echo "  -> To: {$status->to_status_id} ({$toStatus->status_name})\n";
    }
    echo "\n";
}

echo "=== Workflows TO IOT In Progress (340) ===\n";
$workflowsToInProgress = \App\Models\NewWorkFlowStatuses::with('workflow')
    ->where('to_status_id', $iotInProgress)
    ->get();

foreach ($workflowsToInProgress as $workflowStatus) {
    $workflow = $workflowStatus->workflow;
    $fromStatus = \App\Models\Status::find($workflow->from_status_id);
    echo "From: {$workflow->from_status_id} ({$fromStatus->status_name})\n";
    echo "Workflow ID: {$workflow->id}, Same Time: {$workflow->same_time}\n";
    echo "  -> To: {$workflowStatus->to_status_id} (IOT In progress)\n\n";
}
