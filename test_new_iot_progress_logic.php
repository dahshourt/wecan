<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing NEW IOT In Progress Logic (active=0 then active=1) ===\n";

$crId = 31351;

// Clean up any existing IOT In progress status
echo "\n1. Cleaning up existing IOT In progress status for test...\n";
ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 340) // IOT In progress
    ->delete();
echo "  - Removed existing IOT In progress statuses\n";

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

echo "\n3. Current state before first transition:\n";
echo "  - QC Review: Active " . ($qcStatus ? $qcStatus->active : 'NOT FOUND') . "\n";
echo "  - Vendor Review: Active " . ($vendorStatus ? $vendorStatus->active : 'NOT FOUND') . "\n";

$iotService = new IotTcsFlowService();
$validUser = \App\Models\User::first();
$userId = $validUser ? $validUser->id : 1;
$context = [
    'user_id' => $userId,
    'application_id' => 1,
];

echo "\n4. Testing FIRST transition: QC Review → IOT In progress (should create active=0)...\n";
$statusData = [
    'old_status_id' => 338, // IOT TCs Review QC
    'new_status_id' => 9083, // Workflow to IOT In progress
];

$isIotTransition = $iotService->isIotTcsTransition($crId, $statusData);
echo "  Is IOT transition: " . ($isIotTransition ? "YES" : "NO") . "\n";

if ($isIotTransition) {
    $activeFlag = $iotService->handleIotTcsTransition($crId, $statusData, $context);
    echo "  Active flag returned: '{$activeFlag}'\n";
    
    // Check results
    $iotInProgressCreated = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 340) // IOT In progress
        ->where('active', '0')
        ->exists();
    
    $qcStatusAfter = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 338)
        ->orderBy('id', 'desc')
        ->first();
    
    echo "  IOT In progress created (active=0): " . ($iotInProgressCreated ? "YES ✅" : "NO ❌") . "\n";
    echo "  QC Review completed (active=2): " . ($qcStatusAfter && $qcStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
}

echo "\n5. Testing SECOND transition: Vendor Review → IOT In progress (should activate to active=1)...\n";
$statusData2 = [
    'old_status_id' => 339, // IOT TCs Review vendor
    'new_status_id' => 9084, // Workflow to IOT In progress
];

$isIotTransition2 = $iotService->isIotTcsTransition($crId, $statusData2);
echo "  Is IOT transition: " . ($isIotTransition2 ? "YES" : "NO") . "\n";

if ($isIotTransition2) {
    $activeFlag2 = $iotService->handleIotTcsTransition($crId, $statusData2, $context);
    echo "  Active flag returned: '{$activeFlag2}'\n";
    
    // Check results
    $iotInProgressActive = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 340) // IOT In progress
        ->where('active', '1')
        ->exists();
    
    $vendorStatusAfter = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 339)
        ->orderBy('id', 'desc')
        ->first();
    
    echo "  IOT In progress activated (active=1): " . ($iotInProgressActive ? "YES ✅" : "NO ❌") . "\n";
    echo "  Vendor Review completed (active=2): " . ($vendorStatusAfter && $vendorStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
}

echo "\n6. Final state check:\n";
$allIotInProgress = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 340)
    ->orderBy('id', 'desc')
    ->get();

echo "  IOT In progress records:\n";
foreach ($allIotInProgress as $record) {
    echo "    - ID: {$record->id}, Active: {$record->active}, Created: {$record->created_at}\n";
}

echo "\n=== Test Complete ===\n";
