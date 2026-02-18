<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking table structures ===\n";

// Check new_workflow_statuses table structure
echo "\nnew_workflow_statuses table columns:\n";
$columns = DB::select("DESCRIBE new_workflow_statuses");
foreach ($columns as $column) {
    echo "  {$column->Field} ({$column->Type})\n";
}

// Check some sample data
echo "\nSample data from new_workflow_statuses:\n";
$sampleData = DB::table('new_workflow_statuses')->limit(5)->get();
foreach ($sampleData as $row) {
    echo "  Row: " . json_encode($row) . "\n";
}

echo "\n=== Done ===\n";
