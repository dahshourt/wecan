<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;

echo "=== CLEANING UP DUPLICATE STATUS RECORDS ===" . PHP_EOL;

// Find all IOT In progress records for CR 31351
$iotRecords = ChangeRequestStatus::where('cr_id', 31351)
    ->where('new_status_id', 340) // IOT In progress
    ->orderBy('created_at', 'desc')
    ->get();

echo "Found " . $iotRecords->count() . " IOT In progress records:" . PHP_EOL;

foreach ($iotRecords as $record) {
    echo "  ID: " . $record->id . " | Active: " . $record->active . " | Created: " . $record->created_at . PHP_EOL;
}

// Keep only the newest one as active, mark others as completed
if ($iotRecords->count() > 1) {
    echo PHP_EOL . "=== CLEANING UP ===" . PHP_EOL;
    
    $keepRecord = $iotRecords->first();
    echo "Keeping record ID " . $keepRecord->id . " as active" . PHP_EOL;
    
    foreach ($iotRecords as $record) {
        if ($record->id !== $keepRecord->id) {
            echo "Marking record ID " . $record->id . " as completed (active=2)" . PHP_EOL;
            $record->update(['active' => '2']);
        }
    }
}

// Also clean up any other active records that shouldn't be active
echo PHP_EOL . "=== CHECKING OTHER ACTIVE RECORDS ===" . PHP_EOL;

$otherActiveRecords = ChangeRequestStatus::where('cr_id', 31351)
    ->where('active', '1')
    ->where('new_status_id', '!=', 340)
    ->get();

if ($otherActiveRecords->count() > 0) {
    echo "Found " . $otherActiveRecords->count() . " other active records that should be completed:" . PHP_EOL;
    
    foreach ($otherActiveRecords as $record) {
        echo "  Marking record ID " . $record->id . " as completed (active=2)" . PHP_EOL;
        $record->update(['active' => '2']);
    }
} else {
    echo "No other active records found" . PHP_EOL;
}

echo PHP_EOL . "=== FINAL VERIFICATION ===" . PHP_EOL;

$activeRecords = ChangeRequestStatus::where('cr_id', 31351)
    ->where('active', '1')
    ->get();

echo "Active records after cleanup: " . $activeRecords->count() . PHP_EOL;
foreach ($activeRecords as $record) {
    $statusName = \App\Models\Status::find($record->new_status_id);
    echo "  ID: " . $record->id . " | Status: " . ($statusName ? $statusName->status_name : 'N/A') . PHP_EOL;
}

echo PHP_EOL . "✅ Cleanup complete! Now try the interface transition again." . PHP_EOL;
