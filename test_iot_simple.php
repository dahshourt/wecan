<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Simple IOT Test\n";
echo "================\n";

try {
    $service = new \App\Services\ChangeRequest\Status\ChangeRequestStatusService(
        new \App\Services\ChangeRequest\Status\ChangeRequestStatusValidator(),
        new \App\Services\ChangeRequest\Status\ChangeRequestStatusCreator(),
        new \App\Services\ChangeRequest\Status\ChangeRequestStatusContextFactory(),
        new \App\Services\ChangeRequest\Status\ChangeRequestEventService()
    );
    echo "✅ Service created successfully\n";
    
    // Test if IOT methods exist
    if (method_exists($service, 'areBothIotPendingStatusesActive')) {
        echo "✅ areBothIotPendingStatusesActive method exists\n";
    } else {
        echo "❌ areBothIotPendingStatusesActive method NOT found\n";
    }
    
    if (method_exists($service, 'handleIotInProgressTransition')) {
        echo "✅ handleIotInProgressTransition method exists\n";
    } else {
        echo "❌ handleIotInProgressTransition method NOT found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nTest complete.\n";
