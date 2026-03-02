<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== CR 31351 Current Status ===" . PHP_EOL;
$cr = ChangeRequest::find(31351);
if ($cr) {
    echo "CR ID: " . $cr->id . PHP_EOL;
    echo "CR No: " . $cr->cr_no . PHP_EOL;
    echo "Current Status ID: " . $cr->status_id . PHP_EOL;
    
    // Get status name
    $status = Status::find($cr->status_id);
    echo "Current Status Name: " . ($status ? $status->status_name : 'N/A') . PHP_EOL;
    
    echo "Title: " . $cr->title . PHP_EOL;
} else {
    echo "CR 31351 not found" . PHP_EOL;
}

echo PHP_EOL . "=== CR 31351 Status History ===" . PHP_EOL;
$statuses = ChangeRequestStatus::where('cr_id', 31351)
    ->orderBy('id', 'desc')
    ->limit(15)
    ->get();

foreach ($statuses as $status) {
    $oldStatus = Status::find($status->old_status_id);
    $newStatus = Status::find($status->new_status_id);
    
    echo "ID: " . $status->id . 
         " | Old: " . ($oldStatus ? $oldStatus->status_name : $status->old_status_id) .
         " -> New: " . ($newStatus ? $newStatus->status_name : $status->new_status_id) .
         " | Active: " . $status->active .
         " | Created: " . $status->created_at .
         PHP_EOL;
}

echo PHP_EOL . "=== Check for 'Fix Defect-3rd Parties' and 'IOT In progress' statuses ===" . PHP_EOL;

$fixDefectStatus = Status::where('status_name', 'Fix Defect-3rd Parties')->first();
$iotInProgressStatus = Status::where('status_name', 'IOT In progress')->first();

echo "Fix Defect-3rd Parties Status ID: " . ($fixDefectStatus ? $fixDefectStatus->id : 'Not found') . PHP_EOL;
echo "IOT In progress Status ID: " . ($iotInProgressStatus ? $iotInProgressStatus->id : 'Not found') . PHP_EOL;

if ($fixDefectStatus && $iotInProgressStatus) {
    $fixDefectRecords = ChangeRequestStatus::where('cr_id', 31351)
        ->where('new_status_id', $fixDefectStatus->id)
        ->orderBy('id', 'desc')
        ->get();
    
    $iotRecords = ChangeRequestStatus::where('cr_id', 31351)
        ->where('new_status_id', $iotInProgressStatus->id)
        ->orderBy('id', 'desc')
        ->get();
    
    echo PHP_EOL . "Records for Fix Defect-3rd Parties: " . $fixDefectRecords->count() . PHP_EOL;
    foreach ($fixDefectRecords as $record) {
        echo "  ID: " . $record->id . " | Active: " . $record->active . " | Created: " . $record->created_at . PHP_EOL;
    }
    
    echo PHP_EOL . "Records for IOT In progress: " . $iotRecords->count() . PHP_EOL;
    foreach ($iotRecords as $record) {
        echo "  ID: " . $record->id . " | Active: " . $record->active . " | Created: " . $record->created_at . PHP_EOL;
    }
}
