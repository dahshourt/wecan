<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Testing IOT Status Constants with Reflection\n";
echo "==========================================\n";

// Test 1: Check if IOT status constants are properly loaded
echo "1. Testing IOT Status Constants:\n";

$service = new \App\Services\ChangeRequest\ChangeRequestStatusService();
$reflection = new ReflectionClass($service);

$constants = [
    'PENDING_IOT_TCS_REVIEW_QC_STATUS_ID',
    'PENDING_IOT_TCS_REVIEW_SA_STATUS_ID', 
    'IOT_TCS_REVIEW_QC_STATUS_ID',
    'IOT_TCS_REVIEW_VENDOR_STATUS_ID',
    'IOT_IN_PROGRESS_STATUS_ID'
];

$allDefined = true;
foreach ($constants as $constant) {
    try {
        $property = $reflection->getProperty($constant);
        $property->setAccessible(true);
        $value = $property->getValue($service);
        
        if ($value !== null) {
            echo "   ✓ {$constant}: {$value}\n";
            $allDefined = $allDefined && $value !== null;
        } else {
            echo "   ✗ {$constant}: NOT DEFINED\n";
            $allDefined = false;
        }
    } catch (Exception $e) {
        echo "   ✗ {$constant}: ERROR - " . $e->getMessage() . "\n";
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

echo "\n";

// Test 2: Check if methods exist
echo "2. Testing IOT Methods:\n";

$methods = [
    'areBothIotPendingStatusesActive',
    'getIotInProgressWorkflowId',
    'handleIotInProgressTransition'
];

$allMethodsExist = true;
foreach ($methods as $method) {
    try {
        $methodRef = $reflection->getMethod($method);
        echo "   ✓ {$method}: EXISTS\n";
    } catch (Exception $e) {
        echo "   ✗ {$method}: NOT FOUND\n";
        $allMethodsExist = false;
    }
}

echo "\n";
echo "Methods Result: " . ($allMethodsExist ? "ALL EXIST" : "SOME NOT FOUND") . "\n";

if ($allMethodsExist) {
    echo "✅ All IOT methods are properly defined\n";
} else {
    echo "❌ Some IOT methods are missing\n";
}
