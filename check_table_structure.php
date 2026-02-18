<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking Change Request table structure\n";
echo "====================================\n\n";

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('change_request');
echo "All columns:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}

echo "\nCheck complete.\n";
