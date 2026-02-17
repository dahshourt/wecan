<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Searching for IOT Review statuses:\n";

$statuses = \App\Models\Status::where('status_name', 'like', '%IOT%Review%')
    ->orWhere('status_name', 'like', '%Pending%IOT%')
    ->orWhere('status_name', 'like', '%IOT%Progress%')
    ->get(['id', 'status_name']);

foreach ($statuses as $status) {
    echo $status->id . ': ' . $status->status_name . "\n";
}

echo "\nSearching for all IOT statuses:\n";

$allIotStatuses = \App\Models\Status::where('status_name', 'like', '%IOT%')
    ->get(['id', 'status_name']);

foreach ($allIotStatuses as $status) {
    echo $status->id . ': ' . $status->status_name . "\n";
}
