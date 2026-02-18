<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking Active Records for CR 31351\n";
echo "==================================\n\n";

$crId = 31351;

// Check all active records
$activeRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('active', 1)
    ->get();

echo "Active records found: {$activeRecords->count()}\n\n";

foreach ($activeRecords as $record) {
    echo "Record ID: {$record->id}\n";
    echo "New Status ID: {$record->new_status_id}\n";
    echo "Old Status ID: {$record->old_status_id}\n";
    echo "Created: {$record->created_at}\n";
    
    $status = \App\Models\Status::find($record->new_status_id);
    if ($status) {
        echo "Status Name: {$status->status_name}\n";
    }
    echo "---\n";
}

// Also check the most recent record with new_status_id = 340
$latestIotRecord = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', 340)
    ->orderBy('created_at', 'desc')
    ->first();

if ($latestIotRecord) {
    echo "\nLatest IOT In progress record:\n";
    echo "Record ID: {$latestIotRecord->id}\n";
    echo "Active: {$latestIotRecord->active}\n";
    echo "Created: {$latestIotRecord->created_at}\n";
}
