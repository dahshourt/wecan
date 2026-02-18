<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing Update IOT TCs Transition Logic ===\n";

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

// Set up active review statuses for testing
echo "\n2. Setting up test scenario with active review statuses...\n";

$qcStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338) // IOT TCs Review QC
    ->orderBy('id', 'desc')
    ->first();

$vendorStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339) // IOT TCs Review vendor
    ->orderBy('id', 'desc')
    ->first();

// Make sure both are active for testing
if ($qcStatus && $qcStatus->active != '1') {
    $qcStatus->update(['active' => '1']);
    echo "  - Set QC Review to active\n";
}

if ($vendorStatus && $vendorStatus->active != '1') {
    $vendorStatus->update(['active' => '1']);
    echo "  - Set Vendor Review to active\n";
}

echo "\n3. Current state:\n";
echo "  - QC Review: Active " . ($qcStatus ? $qcStatus->active : 'NOT FOUND') . "\n";
echo "  - Vendor Review: Active " . ($vendorStatus ? $vendorStatus->active : 'NOT FOUND') . "\n";

// Test the transition logic
$iotService = new IotTcsFlowService();

echo "\n4. Testing QC Review to Update IOT TCs transition:\n";
$statusData = [
    'old_status_id' => 338, // IOT TCs Review QC
    'new_status_id' => 9085, // Workflow to Update IOT TCs
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
    
    // Check if Update IOT TCs was created
    $updateIotCreated = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 341)
        ->where('active', '1')
        ->exists();
    
    echo "  Update IOT TCs created: " . ($updateIotCreated ? "YES ✅" : "NO ❌") . "\n";
    
    // Check if QC Review was completed
    $qcStatusAfter = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 338)
        ->orderBy('id', 'desc')
        ->first();
    
    echo "  QC Review completed (active=2): " . ($qcStatusAfter && $qcStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
}

echo "\n5. Testing Vendor Review to Update IOT TCs transition:\n";
$statusData = [
    'old_status_id' => 339, // IOT TCs Review vendor
    'new_status_id' => 9086, // Workflow to Update IOT TCs
];

$isIotTransition = $iotService->isIotTcsTransition($crId, $statusData);
echo "  Is IOT transition: " . ($isIotTransition ? "YES" : "NO") . "\n";

if ($isIotTransition) {
    // First clean up the previous Update IOT TCs to test fresh
    ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 341)
        ->where('active', '1')
        ->delete();
    
    $activeFlag = $iotService->handleIotTcsTransition($crId, $statusData, $context);
    echo "  Active flag returned: '{$activeFlag}'\n";
    
    // Check if Update IOT TCs was created
    $updateIotCreated = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 341)
        ->where('active', '1')
        ->exists();
    
    echo "  Update IOT TCs created: " . ($updateIotCreated ? "YES ✅" : "NO ❌") . "\n";
    
    // Check if Vendor Review was completed
    $vendorStatusAfter = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 339)
        ->orderBy('id', 'desc')
        ->first();
    
    echo "  Vendor Review completed (active=2): " . ($vendorStatusAfter && $vendorStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
}

echo "\n=== Test Complete ===\n";
