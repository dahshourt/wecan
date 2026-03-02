<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING CHANGE_REQUEST TABLE STRUCTURE ===" . PHP_EOL;

// Get the table columns
$columns = Schema::getColumnListing('change_request');
echo "Columns in change_request table:" . PHP_EOL;
foreach ($columns as $column) {
    echo "  - " . $column . PHP_EOL;
}

echo PHP_EOL . "=== CHECKING CR 31351 CURRENT STATE ===" . PHP_EOL;
$cr = Change_request::find(31351);
if ($cr) {
    echo "CR ID: " . $cr->id . PHP_EOL;
    echo "CR No: " . $cr->cr_no . PHP_EOL;
    echo "Available attributes:" . PHP_EOL;
    foreach ($cr->getAttributes() as $key => $value) {
        echo "  " . $key . ": " . ($value ?? 'NULL') . PHP_EOL;
    }
}
