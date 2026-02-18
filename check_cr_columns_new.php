<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking Change Request table columns\n";
echo "===================================\n\n";

$cr = \App\Models\Change_request::find(31351);
echo "CR found: {$cr->cr_no}\n";

// Check what status-related columns exist
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('change_request');
echo "Available columns:\n";
foreach ($columns as $column) {
    if (strpos($column, 'status') !== false) {
        echo "- {$column}\n";
    }
}

echo "\nCurrent CR status value: '{$cr->status_id}'\n";
echo "\nCheck complete.\n";
