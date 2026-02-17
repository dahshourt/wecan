<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== Checking CR 2023 Status History ===\n";

$statuses = ChangeRequestStatus::where('cr_id', 2023)
    ->orderBy('id', 'asc')
    ->get(['id', 'old_status_id', 'new_status_id', 'active', 'created_at']);

echo "Total status records: " . $statuses->count() . "\n\n";

foreach ($statuses as $status) {
    $oldStatus = Status::find($status->old_status_id);
    $newStatus = Status::find($status->new_status_id);
    
    echo "ID: {$status->id}\n";
    echo "  Old: " . ($oldStatus ? $oldStatus->status_name : 'N/A') . " (ID: {$status->old_status_id})\n";
    echo "  New: " . ($newStatus ? $newStatus->status_name : 'N/A') . " (ID: {$status->new_status_id})\n";
    echo "  Active: {$status->active}\n";
    echo "  Created: {$status->created_at}\n";
    echo "  ---\n";
}

echo "\n=== Current Active Statuses ===\n";
$activeStatuses = ChangeRequestStatus::where('cr_id', 2023)
    ->where('active', '1')
    ->with(['oldStatus', 'newStatus'])
    ->get();

foreach ($activeStatuses as $status) {
    echo "Active: " . ($status->newStatus ? $status->newStatus->status_name : 'N/A') . "\n";
}
