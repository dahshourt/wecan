<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Fixing CR 31351 Status\n";
echo "====================\n\n";

$crId = 31351;

// 1. Get current CR info
$cr = \App\Models\Change_request::find($crId);
if (!$cr) {
    echo "CR NOT FOUND\n";
    exit;
}

echo "Current CR Status ID: '{$cr->status_id}'\n";

// 2. Get the active IOT In progress record
$activeRecord = \App\Models\Change_request_statuse::where('cr_id', $crId)
    ->where('new_status_id', 340) // IOT In progress
    ->where('active', 1)
    ->first();

if ($activeRecord) {
    echo "Found active IOT In progress record: {$activeRecord->id}\n";
    
    // 3. Update the CR's main status
    $cr->update([
        'status_id' => 340,
        'updated_at' => now(),
    ]);
    
    echo "✅ Updated CR main status to 340 (IOT In progress)\n";
    
    // 4. Verify the update
    $cr->refresh();
    echo "New CR Status ID: {$cr->status_id}\n";
    
    $status = \App\Models\Status::find($cr->status_id);
    if ($status) {
        echo "Status Name: {$status->status_name}\n";
    }
} else {
    echo "❌ No active IOT In progress record found\n";
}

echo "\nFix complete.\n";
