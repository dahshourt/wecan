<?php

$crId = 31642;
echo "CR 31642 Debug:" . PHP_EOL;

// Check merge point status
$mergeStatus = \App\Models\Status::where('status_name', 'Pending Update Agreed Requirements')->first();
echo "Merge status ID: " . $mergeStatus->id . PHP_EOL;

// Check merge records
$mergeRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', $mergeStatus->id)
    ->get();
    
echo "Merge records count: " . $mergeRecords->count() . PHP_EOL;
foreach ($mergeRecords as $record) {
    echo "  - Record ID: " . $record->id . ", old_status_id: " . $record->old_status_id . ", new_status_id: " . $record->new_status_id . ", active: " . $record->active . PHP_EOL;
}

// Check both workflows complete
$service = new \App\Services\ChangeRequest\ChangeRequestStatusService();
$bothComplete = $service->areBothWorkflowsCompleteById($crId, $mergeStatus->id);
echo "Both workflows complete: " . ($bothComplete ? 'YES' : 'NO') . PHP_EOL;

// Check pending statuses
$pendingStatuses = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('old_status_id', $mergeStatus->id)
    ->where('active', '0')
    ->get();
    
echo "Pending statuses count: " . $pendingStatuses->count() . PHP_EOL;
foreach ($pendingStatuses as $status) {
    echo "  - Status ID: " . $status->id . ", active: " . $status->active . PHP_EOL;
}
