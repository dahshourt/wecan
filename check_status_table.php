<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== CHANGE_REQUEST_STATUSES TABLE STRUCTURE ===" . PHP_EOL;
$columns = Schema::getColumnListing('change_request_statuses');
foreach ($columns as $col) {
    echo "  - $col" . PHP_EOL;
}
