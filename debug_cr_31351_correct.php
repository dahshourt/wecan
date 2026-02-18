<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Debugging CR 31351 (Corrected)\n";
echo "============================\n\n";

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

// 2. Check current active status record
echo "2. Current Active Status Record:\n";
$activeRecord = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('active', 1)
    ->first();

if ($activeRecord) {
    echo "   ✓ Active record found\n";
    echo "   - New Status ID: {$activeRecord->new_status_id}\n";
    echo "   - Old Status ID: {$activeRecord->old_status_id}\n";
    echo "   - User ID: {$activeRecord->user_id}\n";
    echo "   - Created: {$activeRecord->created_at}\n";
    
    // Get status names
    $newStatus = \App\Models\Status::find($activeRecord->new_status_id);
    $oldStatus = \App\Models\Status::find($activeRecord->old_status_id);
    
    if ($newStatus) {
        echo "   - New Status Name: {$newStatus->status_name}\n";
    }
    if ($oldStatus) {
        echo "   - Old Status Name: {$oldStatus->status_name}\n";
    }
} else {
    echo "   ✗ No active status record found\n";
}

echo "\n";

// 3. Check specifically for IOT statuses using new_status_id
echo "3. IOT Status Check (using new_status_id):\n";
$iotStatusIds = [336, 337, 338, 339, 340]; // IOT status IDs
try {
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
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Check if both IOT pending statuses are active
echo "4. IOT Parallel Status Check:\n";
try {
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
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Check all recent records with status names
echo "5. Recent Workflow Activity (last 10 records with names):\n";
try {
    $recentRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    foreach ($recentRecords as $record) {
        $status = \App\Models\Status::find($record->new_status_id);
        $statusName = $status ? $status->status_name : 'Unknown (ID: ' . $record->new_status_id . ')';
        echo "   - {$record->created_at}: {$statusName} (Active: {$record->active})\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 6. Check if there are any IOT workflows available
echo "6. Available IOT Workflows:\n";
$iotWorkflows = \App\Models\NewWorkFlow::whereHas('workflowstatus', function($query) use ($iotStatusIds) {
    $query->whereIn('to_status_id', $iotStatusIds);
})->orWhereHas('workflowstatus', function($query) use ($iotStatusIds) {
    $query->whereIn('from_status_id', $iotStatusIds);
})->get();

if ($iotWorkflows->isEmpty()) {
    echo "   No IOT workflows found\n";
} else {
    foreach ($iotWorkflows as $workflow) {
        echo "   - Workflow ID: {$workflow->id}, Same Time: {$workflow->same_time}\n";
        foreach ($workflow->workflowstatus as $ws) {
            if (in_array($ws->from_status_id, $iotStatusIds) || in_array($ws->to_status_id, $iotStatusIds)) {
                $fromStatus = \App\Models\Status::find($ws->from_status_id);
                $toStatus = \App\Models\Status::find($ws->to_status_id);
                $fromName = $fromStatus ? $fromStatus->status_name : 'Unknown';
                $toName = $toStatus ? $toStatus->status_name : 'Unknown';
                echo "     From: {$fromName} ({$ws->from_status_id}) → To: {$toName} ({$ws->to_status_id})\n";
            }
        }
    }
}

echo "\n";
echo "Debug complete.\n";
