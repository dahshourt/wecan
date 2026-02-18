<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Final Status Verification for CR 31351\n";
echo "====================================\n\n";

$crId = 31351;

// Check CR main status
$cr = \App\Models\Change_request::find($crId);
echo "CR Main Status ID: {$cr->status_id}\n";
if ($cr->status_id) {
    $status = \App\Models\Status::find($cr->status_id);
    if ($status) {
        echo "CR Main Status Name: {$status->status_name}\n";
    }
}

echo "\n";

// Check IOT In Progress status
$iotInProgressRecords = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', 340) // IOT In Progress
    ->orderBy('created_at', 'desc')
    ->get();

echo "IOT In Progress Records:\n";
foreach ($iotInProgressRecords as $record) {
    echo "- ID: {$record->id}, Active: {$record->active}, Created: {$record->created_at}\n";
}

echo "\n";

// Check if we have active IOT In Progress
$activeIotInProgress = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', 340)
    ->where('active', '1')
    ->first();

if ($activeIotInProgress) {
    echo "✅ Active IOT In Progress record found: ID {$activeIotInProgress->id}\n";
    
    // Update CR main status
    $cr->update(['status_id' => 340]);
    echo "✅ Updated CR main status to IOT In Progress (340)\n";
} else {
    echo "❌ No active IOT In Progress record found\n";
}

echo "\nVerification complete.\n";
