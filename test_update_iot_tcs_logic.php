<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== Testing Update IOT TCs Logic (completes IOT In progress) ===\n";

$crId = 31351;

// Clean up any existing Update IOT TCs status
echo "\n1. Cleaning up existing Update IOT TCs status for test...\n";
ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 341) // Update IOT TCs
    ->delete();
echo "  - Removed existing Update IOT TCs statuses\n";

// Set up scenario: Both QC and Vendor reviews are active, and IOT In progress is active
echo "\n2. Setting up test scenario (all statuses active)...\n";

$qcStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338) // IOT TCs Review QC
    ->orderBy('id', 'desc')
    ->first();

$vendorStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339) // IOT TCs Review vendor
    ->orderBy('id', 'desc')
    ->first();

$iotInProgressStatus = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 340) // IOT In progress
    ->orderBy('id', 'desc')
    ->first();

// Make sure all are active
if ($qcStatus && $qcStatus->active != '1') {
    $qcStatus->update(['active' => '1']);
    echo "  - Set QC Review to active\n";
}

if ($vendorStatus && $vendorStatus->active != '1') {
    $vendorStatus->update(['active' => '1']);
    echo "  - Set Vendor Review to active\n";
}

if (!$iotInProgressStatus || $iotInProgressStatus->active != '1') {
    // Create active IOT In progress if it doesn't exist
    $validUser = \App\Models\User::first();
    $userId = $validUser ? $validUser->id : 1;
    $context = [
        'user_id' => $userId,
        'application_id' => 1,
    ];
    
    ChangeRequestStatus::create([
        'cr_id' => $crId,
        'old_status_id' => 338,
        'new_status_id' => 340,
        'group_id' => null,
        'reference_group_id' => null,
        'previous_group_id' => null,
        'current_group_id' => null,
        'user_id' => $userId,
        'sla' => 0,
        'active' => '1',
        'created_at' => now(),
        'updated_at' => null,
    ]);
    echo "  - Created active IOT In progress\n";
}

echo "\n3. Current state before transition:\n";
$qcStatusBefore = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338)
    ->orderBy('id', 'desc')
    ->first();

$vendorStatusBefore = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339)
    ->orderBy('id', 'desc')
    ->first();

$iotInProgressBefore = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 340)
    ->orderBy('id', 'desc')
    ->first();

echo "  - QC Review: Active " . ($qcStatusBefore ? $qcStatusBefore->active : 'NOT FOUND') . "\n";
echo "  - Vendor Review: Active " . ($vendorStatusBefore ? $vendorStatusBefore->active : 'NOT FOUND') . "\n";
echo "  - IOT In progress: Active " . ($iotInProgressBefore ? $iotInProgressBefore->active : 'NOT FOUND') . "\n";

$iotService = new IotTcsFlowService();
$validUser = \App\Models\User::first();
$userId = $validUser ? $validUser->id : 1;
$context = [
    'user_id' => $userId,
    'application_id' => 1,
];

echo "\n4. Testing QC Review → Update IOT TCs transition (should complete all other statuses)...\n";
$statusData = [
    'old_status_id' => 338, // IOT TCs Review QC
    'new_status_id' => 9085, // Workflow to Update IOT TCs
];

$isIotTransition = $iotService->isIotTcsTransition($crId, $statusData);
echo "  Is IOT transition: " . ($isIotTransition ? "YES" : "NO") . "\n";

if ($isIotTransition) {
    $activeFlag = $iotService->handleIotTcsTransition($crId, $statusData, $context);
    echo "  Active flag returned: '{$activeFlag}'\n";
    
    // Check results
    $updateIotCreated = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 341) // Update IOT TCs
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
    
    $iotInProgressAfter = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', 340)
        ->orderBy('id', 'desc')
        ->first();
    
    echo "  Update IOT TCs created (active=1): " . ($updateIotCreated ? "YES ✅" : "NO ❌") . "\n";
    echo "  QC Review completed (active=2): " . ($qcStatusAfter && $qcStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
    echo "  Vendor Review completed (active=2): " . ($vendorStatusAfter && $vendorStatusAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
    echo "  IOT In progress completed (active=2): " . ($iotInProgressAfter && $iotInProgressAfter->active == '2' ? "YES ✅" : "NO ❌") . "\n";
}

echo "\n=== Test Complete ===\n";
