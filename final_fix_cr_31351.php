<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== FINAL FIX FOR CR 31351 ===" . PHP_EOL;

// 1. Mark all current statuses as completed
$currentActive = ChangeRequestStatus::where('cr_id', 31351)
    ->where('active', '1')
    ->get();

echo "Marking " . $currentActive->count() . " active statuses as completed:" . PHP_EOL;
foreach ($currentActive as $status) {
    echo "  - ID: " . $status->id . " -> Setting to completed" . PHP_EOL;
    $status->update(['active' => '2']);
}

// 2. Create the new "IOT In progress" status
$iotStatus = Status::find(340); // IOT In progress
if (!$iotStatus) {
    echo "ERROR: IOT In progress status not found!" . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "Creating new IOT In progress status..." . PHP_EOL;

$newStatus = new ChangeRequestStatus();
$newStatus->cr_id = 31351;
$newStatus->old_status_id = 343; // Fix Defect-3rd Parties
$newStatus->new_status_id = 340; // IOT In progress
$newStatus->group_id = null;
$newStatus->reference_group_id = null;
$newStatus->previous_group_id = null;
$newStatus->current_group_id = null;
$newStatus->user_id = 365; // Current user
$newStatus->sla = $iotStatus->sla;
$newStatus->sla_dif = 0;
$newStatus->active = '1'; // Set as active
$newStatus->assignment_user_id = null;
$newStatus->created_at = now();
$newStatus->updated_at = null;

$newStatus->save();

echo "✅ SUCCESS! New status created with ID: " . $newStatus->id . PHP_EOL;

// 3. Verify the fix
echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
$cr = \App\Models\Change_request::find(31351);
$currentStatus = $cr->getCurrentStatus();

if ($currentStatus) {
    echo "✅ CR 31351 current status: " . ($currentStatus->status ? $currentStatus->status->status_name : 'N/A') . PHP_EOL;
    echo "✅ Status ID: " . $currentStatus->new_status_id . PHP_EOL;
    echo "✅ Active: " . $currentStatus->active . PHP_EOL;
    echo "✅ Created: " . $currentStatus->created_at . PHP_EOL;
} else {
    echo "❌ ERROR: No current status found!" . PHP_EOL;
}

echo PHP_EOL . "🎉 CR 31351 is now successfully in 'IOT In progress' status!" . PHP_EOL;
echo "You can continue working with this CR normally." . PHP_EOL;
