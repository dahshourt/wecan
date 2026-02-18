<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing IOT In Progress Transition Fix ===\n";

$crId = 31351;

// Get the current status of CR 31351
echo "\nCurrent status of CR {$crId}:\n";
$currentStatuses = ChangeRequestStatus::where('cr_id', $crId)
    ->where('active', '1')
    ->get();

if ($currentStatuses->isEmpty()) {
    echo "No active statuses found. Checking completed IOT review statuses...\n";
    
    $completedIotStatuses = ChangeRequestStatus::where('cr_id', $crId)
        ->whereIn('new_status_id', [338, 339]) // IOT TCs Review QC, IOT TCs Review vendor
        ->where('active', '2')
        ->orderBy('id', 'desc')
        ->get();
    
    foreach ($completedIotStatuses as $status) {
        $newStatus = Status::find($status->new_status_id);
        echo "Completed: " . ($newStatus ? $newStatus->status_name : 'N/A') . " (ID: {$status->id})\n";
    }
    
    if ($completedIotStatuses->isNotEmpty()) {
        echo "\nTesting transition from completed IOT review to IOT In progress...\n";
        
        // Test the IOT service
        $iotService = new IotTcsFlowService();
        
        foreach ($completedIotStatuses as $status) {
        $newStatus = Status::find($status->new_status_id);
        $statusName = $newStatus ? $newStatus->status_name : 'N/A';
        $oldStatusId = $status->new_status_id;
        
        echo "\nTesting transition from: {$statusName} (ID: {$oldStatusId})\n";
        
        // Check if the service detects this as an IOT transition
        $statusData = [
            'old_status_id' => $oldStatusId,
            'new_status_id' => 9083, // Workflow to IOT In progress (will be resolved to status ID 340)
        ];
        
        $isIotTransition = $iotService->isIotTcsTransition($crId, $statusData);
        echo "  Is IOT transition: " . ($isIotTransition ? "YES" : "NO") . "\n";
        
        if ($isIotTransition) {
            echo "  Service will handle this transition\n";
        } else {
            echo "  Normal workflow will handle this transition\n";
        }
    }
    }
} else {
    foreach ($currentStatuses as $status) {
        $newStatus = Status::find($status->new_status_id);
        echo "Active: " . ($newStatus ? $newStatus->status_name : 'N/A') . " (ID: {$status->id})\n";
    }
}

echo "\n=== Test Complete ===\n";
