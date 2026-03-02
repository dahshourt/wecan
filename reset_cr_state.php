<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;

echo "=== RESETTING CR 31351 FOR INTERFACE TEST ===" . PHP_EOL;

// Mark current IOT In progress as completed
$currentActive = ChangeRequestStatus::where('cr_id', 31351)
    ->where('active', '1')
    ->first();

if ($currentActive) {
    echo "Marking current active status ID " . $currentActive->id . " as completed" . PHP_EOL;
    $currentActive->update(['active' => '2']);
}

// Set Fix Defect-3rd Parties (ID 6384) back to active
$fixDefectStatus = ChangeRequestStatus::find(6384);
if ($fixDefectStatus) {
    echo "Setting Fix Defect-3rd Parties status ID 6384 back to active" . PHP_EOL;
    $fixDefectStatus->update(['active' => '1']);
}

echo "✅ Reset complete. Now CR 31351 is in Fix Defect-3rd Parties status." . PHP_EOL;
echo "Try the interface transition again." . PHP_EOL;
