<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing Fixed Update IOT TCs Transition Logic ===\n";

$crId = 31351;

// Clean up any existing Update IOT TCs status
echo "\n1. Cleaning up existing Update IOT TCs status for test...\n";
$updateIotStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 341) // Update IOT TCs
    ->where('active', '1')
    ->first();

if ($updateIotStatus) {
    $updateIotStatus->delete();
    echo "  - Removed existing Update IOT TCs status\n";
}

// Set up scenario: Both QC and Vendor reviews are active
echo "\n2. Setting up test scenario (both reviews active)...\n";

$qcStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338) // IOT TCs Review QC
    ->orderBy('id', 'desc')
    ->first();

$vendorStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339) // IOT TCs Review vendor
    ->orderBy('id', 'desc')
    ->first();

// Make sure both are active
if ($qcStatus && $qcStatus->active != '1') {
    $qcStatus->update(['active' => '1']);
    echo "  - Set QC Review to active\n";
}

if ($vendorStatus && $vendorStatus->active != '1') {
    $vendorStatus->update(['active' => '1']);
    echo "  - Set Vendor Review to active\n";
}

echo "\n3. Current state before transition:\n";
echo "  - QC Review: Active " . ($qcStatus ? $qcStatus->active : 'NOT FOUND') . "\n";
echo "  - Vendor Review: Active " . ($vendorStatus ? $vendorStatus->active : 'NOT FOUND') . "\n";

// Test the transition
$iotService = new IotTcsFlowService();

echo "\n4. Testing Vendor Review to Update IOT TCs transition (should complete QC too)...\n";
$statusData = [
    'old_status_id' => 339, // IOT TCs Review vendor
    'new_status_id' => 9086, // Workflow to Update IOT TCs
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
    
    // Check results
    $updateIotCreated = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 341)
        ->where('active', '1')
        ->exists();
    
    $qcStatusAfter = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 338)
        ->orderBy('id', 'desc')
        ->first();
    
    $vendorStatusAfter = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 339)
        ->orderBy('id', 'desc')
        ->first();
    
    echo "  Update IOT TCs created: " . ($updateIotCreated ? "YES ✅" : "NO ❌") . "\n";
    echo "  QC Review completed (active=2): " . ($qcStatusAfter && $qcStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
    echo "  Vendor Review completed (active=2): " . ($vendorStatusAfter && $vendorStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
}

echo "\n=== Test Complete ===\n";
