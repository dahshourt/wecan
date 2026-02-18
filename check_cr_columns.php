<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking Change Request Table Structure\n";
echo "======================================\n";

try {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('change_request');
    echo "Columns in change_request table:\n";
    foreach ($columns as $column) {
        echo "   - {$column}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Check the CR record
$cr = \App\Models\Change_request::find(31351);
if ($cr) {
    echo "CR 31351 data:\n";
    foreach ($columns as $column) {
        $value = $cr->$column;
        echo "   {$column}: " . var_export($value, true) . "\n";
    }
}
