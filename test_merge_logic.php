<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing IOT Merge Logic ===\n";

$crId = 31351;

// Check current status of both IOT review statuses
echo "\nCurrent status of IOT review statuses for CR {$crId}:\n";

$qcStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338) // IOT TCs Review QC
    ->orderBy('id', 'desc')
    ->first();

$vendorStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339) // IOT TCs Review vendor
    ->orderBy('id', 'desc')
    ->first();

$qcStatusName = $qcStatus ? Status::find($qcStatus->new_status_id)->status_name : 'NOT FOUND';
$vendorStatusName = $vendorStatus ? Status::find($vendorStatus->new_status_id)->status_name : 'NOT FOUND';

echo "QC Review: {$qcStatusName} (Active: {$qcStatus->active})\n";
echo "Vendor Review: {$vendorStatusName} (Active: {$vendorStatus->active})\n";

// Check if IOT In progress already exists
$iotInProgressExists = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 340) // IOT In progress
    ->exists();

echo "\nIOT In progress exists: " . ($iotInProgressExists ? "YES" : "NO") . "\n";

if (!$iotInProgressExists && $qcStatus->active == '2' && $vendorStatus->active == '2') {
    echo "\nBoth review statuses are completed but IOT In progress doesn't exist yet.\n";
    echo "This is the perfect scenario to test the merge logic!\n";
    
    // Test what happens when we trigger a transition
    $iotService = new IotTcsFlowService();
    
    echo "\nTesting QC to IOT In progress transition (should create IOT In progress):\n";
    $statusData = [
        'old_status_id' => 338, // IOT TCs Review QC
        'new_status_id' => 9083, // Workflow to IOT In progress
    ];
    
    $isIotTransition = $iotService->isIotTcsTransition($crId, $statusData);
    echo "  Is IOT transition: " . ($isIotTransition ? "YES" : "NO") . "\n";
    
    if ($isIotTransition) {
        echo "  Simulating the transition...\n";
        
        // Simulate the transition (this would normally be called from the workflow system)
        $context = [
            'user_id' => 1,
            'application_id' => 1,
        ];
        
        try {
            $activeFlag = $iotService->handleIotTcsTransition($crId, $statusData, $context);
            echo "  Active flag returned: '{$activeFlag}'\n";
            
            // Check if IOT In progress was created
            $iotInProgressCreated = ChangeRequestStatus::where('cr_id', $crId)
                ->where('new_status_id', 340)
                ->where('active', '1')
                ->exists();
            
            echo "  IOT In progress created with active=1: " . ($iotInProgressCreated ? "YES ✅" : "NO ❌") . "\n";
            
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "\nConditions not met for testing merge logic:\n";
    echo "- IOT In progress exists: " . ($iotInProgressExists ? "YES" : "NO") . "\n";
    echo "- QC completed: " . ($qcStatus && $qcStatus->active == '2' ? "YES" : "NO") . "\n";
    echo "- Vendor completed: " . ($vendorStatus && $vendorStatus->active == '2' ? "YES" : "NO") . "\n";
}

echo "\n=== Test Complete ===\n";
