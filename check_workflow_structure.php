<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== NEW_WORKFLOW TABLE STRUCTURE ===" . PHP_EOL;
$columns = Schema::getColumnListing('new_workflow');
foreach ($columns as $column) {
    echo "- " . $column . PHP_EOL;
}

echo PHP_EOL . "=== WORKFLOW_STATUS TABLE STRUCTURE ===" . PHP_EOL;
$workflowStatusColumns = Schema::getColumnListing('workflow_status');
foreach ($workflowStatusColumns as $column) {
    echo "- " . $column . PHP_EOL;
}
