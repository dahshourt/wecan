<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing Partial IOT Merge Scenario ===\n";

$crId = 31351;

// Clean up any existing IOT In progress status
echo "\n1. Cleaning up IOT In progress status for test...\n";
$iotInProgressStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 340)
    ->where('active', '1')
    ->first();

if ($iotInProgressStatus) {
    $iotInProgressStatus->delete();
    echo "  - Removed IOT In progress status\n";
}

// Make sure QC is completed but vendor is still active
echo "\n2. Setting up test scenario (QC completed, vendor still active)...\n";

$qcStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338)
    ->orderBy('id', 'desc')
    ->first();

$vendorStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339)
    ->orderBy('id', 'desc')
    ->first();

// Ensure QC is completed
if ($qcStatus && $qcStatus->active != '2') {
    $qcStatus->update(['active' => '2']);
    echo "  - Set QC Review to completed\n";
}

// Ensure vendor is active
if ($vendorStatus && $vendorStatus->active != '1') {
    $vendorStatus->update(['active' => '1']);
    echo "  - Set Vendor Review to active\n";
}

echo "\n3. Current state:\n";
echo "  - QC Review: Active " . ($qcStatus ? $qcStatus->active : 'NOT FOUND') . "\n";
echo "  - Vendor Review: Active " . ($vendorStatus ? $vendorStatus->active : 'NOT FOUND') . "\n";

// Test that merge doesn't happen when only one is completed
if ($qcStatus && $vendorStatus && $qcStatus->active == '2' && $vendorStatus->active == '1') {
    echo "\n4. Testing merge logic with only QC completed (should NOT create IOT In progress)...\n";
    
    $iotService = new IotTcsFlowService();
    
    // Test QC to IOT In progress transition
    $statusData = [
        'old_status_id' => 338, // IOT TCs Review QC
        'new_status_id' => 9083, // Workflow to IOT In progress
    ];
    
    $isIotTransition = $iotService->isIotTcsTransition($crId, $statusData);
    echo "  Is IOT transition: " . ($isIotTransition ? "YES" : "NO") . "\n";
    
    if ($isIotTransition) {
        $validUser = \App\Models\User::first();
        $userId = $validUser ? $validUser->id : 1;
        
        $context = [
            'user_id' => $userId,
            'application_id' => 1,
        ];
        
        $activeFlag = $iotService->handleIotTcsTransition($crId, $statusData, $context);
        echo "  Active flag returned: '{$activeFlag}'\n";
        
        // Check if IOT In progress was created (it shouldn't be)
        $iotInProgressCreated = ChangeRequestStatus::where('cr_id', $crId)
            ->where('new_status_id', 340)
            ->where('active', '1')
            ->exists();
        
        echo "  IOT In progress created: " . ($iotInProgressCreated ? "YES ❌ (WRONG!)" : "NO ✅ (CORRECT!)") . "\n";
    }
    
    // Now complete vendor and test again
    echo "\n5. Completing vendor review and testing merge again...\n";
    $vendorStatus->update(['active' => '2']);
    echo "  - Vendor Review set to completed\n";
    
    echo "  Testing merge again (should create IOT In progress):\n";
    $activeFlag = $iotService->handleIotTcsTransition($crId, $statusData, $context);
    echo "  Active flag returned: '{$activeFlag}'\n";
    
    $iotInProgressCreated = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 340)
        ->where('active', '1')
        ->exists();
    
    echo "  IOT In progress created: " . ($iotInProgressCreated ? "YES ✅ (CORRECT!)" : "NO ❌ (WRONG!)") . "\n";
}

echo "\n=== Test Complete ===\n";
