<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Testing IOT Workflow on CR 31351\n";
echo "================================\n\n";

$crId = 31351;

// Get the IOT service
$iotService = new \App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService();

// Test 1: QC Transition (should be independent)
echo "1. Testing QC Transition (Pending IOT TCs Review QC → IOT TCs Review QC)\n";
echo "   This should be completely independent...\n";

$qcStatusData = [
    'old_status_id' => 336, // Pending IOT TCs Review QC
    'new_status_id' => 338, // IOT TCs Review QC (workflow ID)
    'user_id' => 1,
    'comment' => 'Testing QC independent transition'
];

$context = [
    'user_id' => 1,
    'application_id' => 1,
];

// Check if this is an IOT transition
$isIotTransition = $iotService->isIotTcsTransition($crId, $qcStatusData);
echo "   Is IOT Transition: " . ($isIotTransition ? "YES" : "NO") . "\n";

if ($isIotTransition) {
    $activeFlag = $iotService->handleIotTcsTransition($crId, $qcStatusData, $context);
    echo "   QC Transition completed with active flag: {$activeFlag}\n";
}

echo "\n";

// Test 2: SA Transition (should merge both workflows)
echo "2. Testing SA Transition (Pending IOT TCs Review SA → IOT TCs Review vendor → IOT In Progress)\n";
echo "   This should immediately merge both workflows...\n";

$saStatusData = [
    'old_status_id' => 337, // Pending IOT TCs Review SA
    'new_status_id' => 339, // IOT TCs Review vendor (workflow ID)
    'user_id' => 1,
    'comment' => 'Testing SA merge transition'
];

// Check if this is an IOT transition
$isIotTransition = $iotService->isIotTcsTransition($crId, $saStatusData);
echo "   Is IOT Transition: " . ($isIotTransition ? "YES" : "NO") . "\n";

if ($isIotTransition) {
    $activeFlag = $iotService->handleIotTcsTransition($crId, $saStatusData, $context);
    echo "   SA Transition completed with active flag: {$activeFlag}\n";
}

echo "\n";

// Verify the results
echo "3. Verification - Check final status records:\n";

$finalStatusRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->whereIn('new_status_id', [336, 337, 338, 339, 340]) // IOT statuses
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($finalStatusRecords as $record) {
    $status = \App\Models\Status::find($record->new_status_id);
    $statusName = $status ? $status->status_name : 'Unknown';
    echo "   - {$record->created_at}: {$statusName} (Active: {$record->active})\n";
}

echo "\n";

// Check if IOT In Progress was created with active=1
$iotInProgressActive = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', 340) // IOT In Progress
    ->where('active', 1)
    ->exists();

echo "4. Final Result:\n";
echo "   IOT In Progress created with active=1: " . ($iotInProgressActive ? "YES ✅" : "NO ❌") . "\n";

echo "\nTest complete.\n";
