<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Change_request_statuse;

// Find CRs with parallel statuses
$parallelStatusIds = [293, 294, 295, 292]; // IDs from our test

$crsWithParallelStatuses = Change_request_statuse::whereIn('new_status_id', $parallelStatusIds)
    ->where('active', '1')
    ->distinct('cr_id')
    ->pluck('cr_id');

echo "CRs with parallel statuses: " . $crsWithParallelStatuses->count() . "\n";

if ($crsWithParallelStatuses->count() > 0) {
    echo "CR IDs: " . implode(', ', $crsWithParallelStatuses->take(5)->toArray()) . "\n";
    
    // Test the first one
    $firstCrId = $crsWithParallelStatuses->first();
    echo "\nTesting with CR ID: $firstCrId\n";
    
    // Run the test command
    passthru("cd " . __DIR__ . " && php artisan test:handle-need-update-action $firstCrId");
} else {
    echo "No CRs found with parallel statuses. Testing with a random CR...\n";
    
    $randomCr = Change_request_statuse::first();
    if ($randomCr) {
        echo "Testing with CR ID: " . $randomCr->cr_id . "\n";
        passthru("cd " . __DIR__ . " && php artisan test:handle-need-update-action " . $randomCr->cr_id);
    }
}
