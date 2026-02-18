<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Models\Change_request;

echo "=== Finding CRs with IOT Statuses ===\n";

// Find any CRs with IOT-related statuses
$iotStatuses = Status::where('status_name', 'like', '%IOT%')->get();
echo "Found IOT statuses:\n";
foreach ($iotStatuses as $status) {
    echo "  - {$status->status_name} (ID: {$status->id})\n";
}

echo "\n=== CRs with IOT statuses ===\n";
$crIdsWithIot = ChangeRequestStatus::whereIn('new_status_id', $iotStatuses->pluck('id'))
    ->distinct('cr_id')
    ->pluck('cr_id')
    ->take(5);

foreach ($crIdsWithIot as $crId) {
    echo "CR {$crId}:\n";
    
    $statuses = ChangeRequestStatus::where('cr_id', $crId)
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();
    
    foreach ($statuses as $status) {
        $oldStatus = Status::find($status->old_status_id);
        $newStatus = Status::find($status->new_status_id);
        $oldName = $oldStatus ? $oldStatus->status_name : 'N/A';
        $newName = $newStatus ? $newStatus->status_name : 'N/A';
        echo "  {$oldName} -> {$newName} (active: {$status->active})\n";
    }
    echo "\n";
}
