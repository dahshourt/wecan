<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Testing IOT Status Constants (Simple Test)\n";
echo "==========================================\n";

// Test 1: Check if IOT status constants are properly loaded
echo "1. Testing IOT Status Constants:\n";

$service = new \App\Services\ChangeRequest\ChangeRequestStatusService();

// Test if constants are defined and not null
$constants = [
    'PENDING_IOT_TCS_REVIEW_QC_STATUS_ID',
    'PENDING_IOT_TCS_REVIEW_SA_STATUS_ID', 
    'IOT_TCS_REVIEW_QC_STATUS_ID',
    'IOT_TCS_REVIEW_VENDOR_STATUS_ID',
    'IOT_IN_PROGRESS_STATUS_ID'
];

$allDefined = true;
foreach ($constants as $constant) {
    $value = $service::$$constant;
    if ($value !== null) {
        echo "   ✓ {$constant}: {$value}\n";
        $allDefined = $allDefined && $value !== null;
    } else {
        echo "   ✗ {$constant}: NOT DEFINED\n";
        $allDefined = false;
    }
}

echo "\n";
echo "Result: " . ($allDefined ? "ALL DEFINED" : "SOME NOT DEFINED") . "\n";

if ($allDefined) {
    echo "✅ All IOT status constants are properly defined and accessible\n";
} else {
    echo "❌ Some IOT status constants are missing\n";
}
