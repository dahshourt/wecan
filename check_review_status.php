<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;

echo "=== Checking review status ===\n";

$crId = 31351;

echo "\nQC Review records (status_id 338):\n";
$qcRecords = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 338)
    ->orderBy('id', 'desc')
    ->get();

foreach ($qcRecords as $record) {
    echo "  ID: {$record->id}, Active: {$record->active}, Created: {$record->created_at}\n";
}

echo "\nVendor Review records (status_id 339):\n";
$vendorRecords = ChangeRequestStatus::where('cr_id', $crId)
    ->where('new_status_id', 339)
    ->orderBy('id', 'desc')
    ->get();

foreach ($vendorRecords as $record) {
    echo "  ID: {$record->id}, Active: {$record->active}, Created: {$record->created_at}\n";
}

echo "\n=== Done ===\n";
