<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== MANUAL STATUS CREATION TEST ===" . PHP_EOL;

try {
    // Get the required data
    $toStatus = Status::find(340); // IOT In progress
    if (!$toStatus) {
        echo "ERROR: IOT In progress status not found!" . PHP_EOL;
        exit(1);
    }
    
    echo "IOT In progress status found: " . $toStatus->status_name . PHP_EOL;
    echo "SLA: " . $toStatus->sla . PHP_EOL;
    
    // Create the status record manually
    $newStatus = new ChangeRequestStatus();
    $newStatus->cr_id = 31351;
    $newStatus->old_status_id = 343; // Fix Defect-3rd Parties
    $newStatus->new_status_id = 340; // IOT In progress
    $newStatus->group_id = null;
    $newStatus->reference_group_id = null;
    $newStatus->previous_group_id = null;
    $newStatus->current_group_id = null;
    $newStatus->user_id = 365; // Current user from logs
    $newStatus->sla = $toStatus->sla;
    $newStatus->sla_dif = 0;
    $newStatus->active = '1';
    $newStatus->assignment_user_id = null;
    $newStatus->created_at = now();
    $newStatus->updated_at = null;
    
    echo "Attempting to create status record..." . PHP_EOL;
    $newStatus->save();
    
    echo "✓ Status record created successfully!" . PHP_EOL;
    echo "New Status ID: " . $newStatus->id . PHP_EOL;
    
    // Verify it was created
    $created = ChangeRequestStatus::find($newStatus->id);
    if ($created) {
        echo "✓ Verification successful - record exists in database" . PHP_EOL;
    } else {
        echo "✗ Verification failed - record not found" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
