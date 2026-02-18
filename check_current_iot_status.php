<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== Current IOT Status for CR 31351 ===\n";

$crId = 31351;

// Get all IOT-related statuses
$iotStatuses = ChangeRequestStatus::where('cr_id', $crId)
    ->whereIn('new_status_id', [336, 337, 338, 339, 340]) // All IOT statuses
    ->orderBy('id', 'desc')
    ->get();

foreach ($iotStatuses as $status) {
    $statusName = Status::find($status->new_status_id);
    echo "ID: {$status->id} - {$statusName->status_name} (Active: {$status->active})\n";
    echo "  Created: {$status->created_at}\n";
}

echo "\n=== Summary ===\n";
$activeStatuses = $iotStatuses->where('active', '1');
$completedStatuses = $iotStatuses->where('active', '2');

echo "Active IOT statuses: {$activeStatuses->count()}\n";
foreach ($activeStatuses as $status) {
    $statusName = Status::find($status->new_status_id);
    echo "  - {$statusName->status_name}\n";
}

echo "Completed IOT statuses: {$completedStatuses->count()}\n";
foreach ($completedStatuses as $status) {
    $statusName = Status::find($status->new_status_id);
    echo "  - {$statusName->status_name}\n";
}
