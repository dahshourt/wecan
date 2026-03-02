<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;

echo "=== CHECKING RECENT STATUS RECORDS ===" . PHP_EOL;

$recent = ChangeRequestStatus::where('cr_id', 31351)
    ->where('created_at', '>', now()->subMinutes(5))
    ->orderBy('created_at', 'desc')
    ->get();

echo "Recent records (last 5 minutes): " . $recent->count() . PHP_EOL;

foreach ($recent as $record) {
    $statusName = \App\Models\Status::find($record->new_status_id);
    echo "ID: " . $record->id . 
         " | Status: " . ($statusName ? $statusName->status_name : 'N/A') .
         " | Active: " . $record->active .
         " | Created: " . $record->created_at .
         PHP_EOL;
}

echo PHP_EOL . "=== CHECKING LAST MINUTE ACTIVITY ===" . PHP_EOL;

$veryRecent = ChangeRequestStatus::where('cr_id', 31351)
    ->where('created_at', '>', now()->subMinute())
    ->orderBy('created_at', 'desc')
    ->get();

echo "Very recent records (last 1 minute): " . $veryRecent->count() . PHP_EOL;

foreach ($veryRecent as $record) {
    $statusName = \App\Models\Status::find($record->new_status_id);
    echo "ID: " . $record->id . 
         " | Status: " . ($statusName ? $statusName->status_name : 'N/A') .
         " | Active: " . $record->active .
         " | Created: " . $record->created_at .
         PHP_EOL;
}
