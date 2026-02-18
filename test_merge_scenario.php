<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing IOT Merge Scenario ===\n";

$crId = 31351;

// First, let's clean up the problematic statuses to create a proper test scenario
echo "\n1. Cleaning up existing IOT statuses for proper test...\n";

// Complete the vendor review status (should have been done already)
$vendorStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339) // IOT TCs Review vendor
    ->where('active', '1')
    ->first();

if ($vendorStatus) {
    $vendorStatus->update(['active' => '2']);
    echo "  - Completed IOT TCs Review vendor (ID: {$vendorStatus->id})\n";
}

// Remove the manually created IOT In progress status
$iotInProgressStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 340) // IOT In progress
    ->where('active', '1')
    ->first();

if ($iotInProgressStatus) {
    $iotInProgressStatus->delete();
    echo "  - Removed manually created IOT In progress (ID: {$iotInProgressStatus->id})\n";
}

// Now check the current state
echo "\n2. Current state after cleanup:\n";
$qcStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338)
    ->orderBy('id', 'desc')
    ->first();

$vendorStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339)
    ->orderBy('id', 'desc')
    ->first();

echo "  - QC Review: Active " . ($qcStatus ? $qcStatus->active : 'NOT FOUND') . "\n";
echo "  - Vendor Review: Active " . ($vendorStatus ? $vendorStatus->active : 'NOT FOUND') . "\n";

// Test the merge logic
if ($qcStatus && $vendorStatus && $qcStatus->active == '2' && $vendorStatus->active == '2') {
    echo "\n3. Perfect! Both review statuses are completed. Testing merge logic...\n";
    
    $iotService = new IotTcsFlowService();
    
    // Test QC to IOT In progress transition
    echo "\n  Testing QC to IOT In progress transition:\n";
    $statusData = [
        'old_status_id' => 338, // IOT TCs Review QC
        'new_status_id' => 9083, // Workflow to IOT In progress
    ];
    
    $isIotTransition = $iotService->isIotTcsTransition($crId, $statusData);
    echo "    Is IOT transition: " . ($isIotTransition ? "YES" : "NO") . "\n";
    
    if ($isIotTransition) {
        // Get a valid user ID
        $validUser = \App\Models\User::first();
        $userId = $validUser ? $validUser->id : 1;
        
        $context = [
            'user_id' => $userId,
            'application_id' => 1,
        ];
        
        $activeFlag = $iotService->handleIotTcsTransition($crId, $statusData, $context);
        echo "    Active flag returned: '{$activeFlag}'\n";
        
        // Check if IOT In progress was created
        $iotInProgressCreated = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', 340)
            ->where('active', '1')
            ->exists();
        
        echo "    IOT In progress created: " . ($iotInProgressCreated ? "YES ✅" : "NO ❌") . "\n";
        
        if ($iotInProgressCreated) {
            $newStatus = ChangeRequestStatus::where('cr_id', $crId)
                ->where('new_status_id', 340)
                ->where('active', '1')
                ->first();
            echo "    New IOT In progress ID: {$newStatus->id}\n";
        }
    }
} else {
    echo "\n3. Conditions not met for merge test\n";
}

echo "\n=== Test Complete ===\n";
