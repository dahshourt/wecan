<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== FIXING CR 31351 STATUS ISSUE ===" . PHP_EOL;

// Find the last "Fix Defect-3rd Parties" status record
$fixDefectStatus = Status::where('status_name', 'Fix Defect-3rd Parties')->first();
if (!$fixDefectStatus) {
    echo "ERROR: Fix Defect-3rd Parties status not found!" . PHP_EOL;
    exit(1);
}

echo "Fix Defect-3rd Parties Status ID: " . $fixDefectStatus->id . PHP_EOL;

// Find the most recent record for this status
$lastStatus = ChangeRequestStatus::where('cr_id', 31351)
    ->where('new_status_id', $fixDefectStatus->id)
    ->orderBy('id', 'desc')
    ->first();

if (!$lastStatus) {
    echo "ERROR: No Fix Defect-3rd Parties status record found for CR 31351!" . PHP_EOL;
    exit(1);
}

echo "Found status record ID: " . $lastStatus->id . PHP_EOL;
echo "Current active flag: " . $lastStatus->active . PHP_EOL;
echo "Created at: " . $lastStatus->created_at . PHP_EOL;

// Fix: Set the status to active (1) so it can be transitioned
echo PHP_EOL . "=== FIXING STATUS ===" . PHP_EOL;
echo "Setting status record ID " . $lastStatus->id . " to active=1..." . PHP_EOL;

$lastStatus->update(['active' => '1']);

echo "✓ Status record updated successfully!" . PHP_EOL;

// Verify the fix
echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
$cr = \App\Models\Change_request::find(31351);
$currentStatus = $cr->getCurrentStatus();

if ($currentStatus) {
    echo "✓ CR now has active status: " . ($currentStatus->status ? $currentStatus->status->status_name : 'N/A') . PHP_EOL;
    echo "✓ Status ID: " . $currentStatus->new_status_id . PHP_EOL;
    echo "✓ Active: " . $currentStatus->active . PHP_EOL;
} else {
    echo "✗ Still no current status found" . PHP_EOL;
}

echo PHP_EOL . "=== NEXT STEPS ===" . PHP_EOL;
echo "Now you can:" . PHP_EOL;
echo "1. Go to: http://localhost:8085/tms/index.php/change_request/31351/edit?reference_status=" . $lastStatus->id . PHP_EOL;
echo "2. Select 'IOT In progress' from the status dropdown" . PHP_EOL;
echo "3. Click Update" . PHP_EOL;
echo "4. The transition should now work correctly!" . PHP_EOL;
