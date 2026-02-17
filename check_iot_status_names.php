<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking IOT Status Names\n";
echo "========================\n\n";

$statuses = \App\Models\Status::where('status_name', 'like', '%Pending IOT TCs Review%')
    ->orWhere('status_name', 'like', '%IOT TCs Review%')
    ->get();

foreach ($statuses as $status) {
    echo "ID: {$status->id} - Name: '{$status->status_name}'\n";
}

echo "\nCheck complete.\n";
