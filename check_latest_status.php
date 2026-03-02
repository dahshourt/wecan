<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== CHECKING LATEST STATUS RECORDS FOR CR 31351 ===" . PHP_EOL;

// Get ALL status records for CR 31351, ordered by creation date
$allStatuses = ChangeRequestStatus::where('cr_id', 31351)
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total status records: " . $allStatuses->count() . PHP_EOL;
echo PHP_EOL;

foreach ($allStatuses as $index => $status) {
    $oldStatus = Status::find($status->old_status_id);
    $newStatus = Status::find($status->new_status_id);
    
    echo "#" . ($index + 1) . " - ID: " . $status->id . 
         " | Old: " . ($oldStatus ? $oldStatus->status_name : $status->old_status_id) .
         " -> New: " . ($newStatus ? $newStatus->status_name : $status->new_status_id) .
         " | Active: " . $status->active .
         " | Created: " . $status->created_at .
         PHP_EOL;
}

echo PHP_EOL . "=== CHECKING FOR 'IOT In progress' RECORDS ===" . PHP_EOL;
$iotStatus = Status::where('status_name', 'IOT In progress')->first();
if ($iotStatus) {
    echo "IOT In progress Status ID: " . $iotStatus->id . PHP_EOL;
    
    $iotRecords = ChangeRequestStatus::where('cr_id', 31351)
        ->where('new_status_id', $iotStatus->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "IOT In progress records found: " . $iotRecords->count() . PHP_EOL;
    foreach ($iotRecords as $record) {
        echo "  ID: " . $record->id . " | Active: " . $record->active . " | Created: " . $record->created_at . PHP_EOL;
    }
} else {
    echo "IOT In progress status not found in database!" . PHP_EOL;
}

echo PHP_EOL . "=== CHECKING ACTIVE RECORDS ===" . PHP_EOL;
$activeRecords = ChangeRequestStatus::where('cr_id', 31351)
    ->where('active', '1')
    ->orderBy('created_at', 'desc')
    ->get();

echo "Active records: " . $activeRecords->count() . PHP_EOL;
foreach ($activeRecords as $record) {
    $newStatus = Status::find($record->new_status_id);
    echo "  ID: " . $record->id . " | Status: " . ($newStatus ? $newStatus->status_name : 'N/A') . " | Created: " . $record->created_at . PHP_EOL;
}
