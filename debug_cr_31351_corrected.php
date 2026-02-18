<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Debugging CR 31351\n";
echo "================\n\n";

$crId = 31351;

// 1. Check if CR exists
echo "1. CR Basic Info:\n";
$cr = \App\Models\Change_request::find($crId);
if ($cr) {
    echo "   ✓ CR Found: {$cr->cr_no}\n";
    echo "   Current Status ID: {$cr->status_id}\n";
    
    // Get status name
    $status = \App\Models\Status::find($cr->status_id);
    if ($status) {
        echo "   Current Status Name: {$status->status_name}\n";
    }
} else {
    echo "   ✗ CR NOT FOUND\n";
    exit;
}

echo "\n";

// 2. Check all active statuses for this CR
echo "2. All Status Records:\n";
$statusRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->orderBy('created_at', 'desc')
    ->get();

if ($statusRecords->isEmpty()) {
    echo "   No status records found\n";
} else {
    foreach ($statusRecords as $record) {
        $status = \App\Models\Status::find($record->new_status_id);
        $statusName = $status ? $status->status_name : 'Unknown';
        echo "   - Status ID: {$record->new_status_id} ({$statusName}) - Active: {$record->active} - Created: {$record->created_at}\n";
    }
}

echo "\n";

// 3. Check specifically for IOT statuses
echo "3. IOT Status Check:\n";
$iotStatusIds = [336, 337, 338, 339, 340]; // IOT status IDs
$iotStatusRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->whereIn('new_status_id', $iotStatusIds)
    ->orderBy('created_at', 'desc')
    ->get();

if ($iotStatusRecords->isEmpty()) {
    echo "   No IOT status records found\n";
} else {
    foreach ($iotStatusRecords as $record) {
        $status = \App\Models\Status::find($record->new_status_id);
        $statusName = $status ? $status->status_name : 'Unknown';
        echo "   - IOT Status ID: {$record->new_status_id} ({$statusName}) - Active: {$record->active} - Created: {$record->created_at}\n";
    }
}

echo "\n";

// 4. Check if both IOT pending statuses are active
echo "4. IOT Parallel Status Check:\n";
$qcPendingActive = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', 336) // Pending IOT TCs Review QC
    ->where('active', 1)
    ->exists();

$saPendingActive = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', 337) // Pending IOT TCs Review SA
    ->where('active', 1)
    ->exists();

echo "   - Pending IOT TCs Review QC (336) Active: " . ($qcPendingActive ? "YES" : "NO") . "\n";
echo "   - Pending IOT TCs Review SA (337) Active: " . ($saPendingActive ? "YES" : "NO") . "\n";
echo "   - Both Active: " . ($qcPendingActive && $saPendingActive ? "YES" : "NO") . "\n";

echo "\n";

// 5. Check recent workflow transitions
echo "5. Recent Workflow Activity (last 10 records):\n";
$recentRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($recentRecords as $record) {
    $status = \App\Models\Status::find($record->new_status_id);
    $statusName = $status ? $status->status_name : 'Unknown';
    echo "   - {$record->created_at}: {$statusName} (Active: {$record->active})\n";
}

echo "\n";
echo "Debug complete.\n";
