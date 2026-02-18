<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Debugging Active Record Issue for CR 31351\n";
echo "==========================================\n\n";

$crId = 31351;

// Check the specific record
$record = \App\Models\Change_request_statuse::find(4877);
if ($record) {
    echo "Record 4877 found:\n";
    echo "CR ID: {$record->cr_id}\n";
    echo "New Status ID: {$record->new_status_id}\n";
    echo "Active: '{$record->active}' (length: " . strlen($record->active) . ")\n";
    echo "Created: {$record->created_at}\n";
    
    // Check if active is actually the string '1'
    if ($record->active === '1') {
        echo "Active is string '1' ✓\n";
    } elseif ($record->active === 1) {
        echo "Active is integer 1 ✓\n";
    } else {
        echo "Active is something else: " . var_export($record->active, true) . "\n";
    }
    
    // Try different active values
    $recordsWithString = \App\Models\Change_request_statuse::where('cr_id', $crId)
        ->where('active', '1')
        ->get();
    echo "\nRecords with active='1' (string): {$recordsWithString->count()}\n";
    
    $recordsWithInt = \App\Models\Change_request_statuse::where('cr_id', $crId)
        ->where('active', 1)
        ->get();
    echo "Records with active=1 (int): {$recordsWithInt->count()}\n";
    
    // Update the CR status directly
    $cr = \App\Models\Change_request::find($crId);
    if ($cr) {
        $cr->update([
            'status_id' => 340, // IOT In progress
            'updated_at' => now(),
        ]);
        echo "\n✅ Updated CR main status to 340 (IOT In progress)\n";
    }
}
