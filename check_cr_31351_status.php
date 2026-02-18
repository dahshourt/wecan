<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$crId = 31351;
echo "Checking all status records for CR $crId\n";
echo "===================================\n\n";

$records = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($records as $record) {
    $status = \App\Models\Status::find($record->new_status_id);
    $statusName = $status ? $status->status_name : 'Unknown';
    echo "Record ID: {$record->id}\n";
    echo "Status: $statusName (ID: {$record->new_status_id})\n";
    echo "Active: {$record->active}\n";
    echo "Created: {$record->created_at}\n";
    echo "---\n";
}
