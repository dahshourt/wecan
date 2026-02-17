<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking IOT Workflow Configuration (Fixed)\n";
echo "==========================================\n\n";

// Get the status IDs we need
$pendingQcStatus = \App\Models\Status::where('status_name', 'Pending IOT TCs Review QC')->first();
$pendingSaStatus = \App\Models\Status::where('status_name', 'Pending IOT TCs Review  SA')->first();
$reviewQcStatus = \App\Models\Status::where('status_name', 'IOT TCs Review QC')->first();
$reviewSaStatus = \App\Models\Status::where('status_name', 'IOT TCs Review vendor')->first();
$iotInProgressStatus = \App\Models\Status::where('status_name', 'IOT In Progress')->first();

echo "Status IDs:\n";
echo "- Pending IOT TCs Review QC: " . ($pendingQcStatus ? $pendingQcStatus->id : 'NOT FOUND') . "\n";
echo "- Pending IOT TCs Review SA: " . ($pendingSaStatus ? $pendingSaStatus->id : 'NOT FOUND') . "\n";
echo "- IOT TCs Review QC: " . ($reviewQcStatus ? $reviewQcStatus->id : 'NOT FOUND') . "\n";
echo "- IOT TCs Review vendor: " . ($reviewSaStatus ? $reviewSaStatus->id : 'NOT FOUND') . "\n";
echo "- IOT In Progress: " . ($iotInProgressStatus ? $iotInProgressStatus->id : 'NOT FOUND') . "\n";

echo "\n";

// Check workflows for QC transition
echo "QC Workflows (Pending IOT TCs Review QC → IOT TCs Review QC):\n";
if ($pendingQcStatus && $reviewQcStatus) {
    $qcWorkflows = \App\Models\NewWorkFlow::where('from_status_id', $pendingQcStatus->id)
        ->whereHas('workflowstatus', function ($query) use ($reviewQcStatus) {
            $query->where('to_status_id', $reviewQcStatus->id);
        })
        ->get();
    
    foreach ($qcWorkflows as $workflow) {
        echo "- Workflow ID: {$workflow->id}, Same Time: {$workflow->same_time}\n";
    }
}

echo "\n";

// Check workflows for SA transition
echo "SA Workflows (Pending IOT TCs Review SA → IOT TCs Review vendor):\n";
if ($pendingSaStatus && $reviewSaStatus) {
    $saWorkflows = \App\Models\NewWorkFlow::where('from_status_id', $pendingSaStatus->id)
        ->whereHas('workflowstatus', function ($query) use ($reviewSaStatus) {
            $query->where('to_status_id', $reviewSaStatus->id);
        })
        ->get();
    
    foreach ($saWorkflows as $workflow) {
        echo "- Workflow ID: {$workflow->id}, Same Time: {$workflow->same_time}\n";
    }
}

echo "\nCheck complete.\n";
