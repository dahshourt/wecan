<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkFlowType;

echo "=== Workflow Types ===\n";
$workflows = WorkFlowType::orderBy('id')->get(['id', 'name']);
foreach ($workflows as $wf) {
    echo "ID: {$wf->id} - Name: {$wf->name}\n";
}

echo "\n=== Statuses ===\n";
$statuses = \App\Models\Status::where('id', 319)->get(['id', 'status_name']);
foreach ($statuses as $status) {
    echo "ID: {$status->id} - Name: {$status->status_name}\n";
}
