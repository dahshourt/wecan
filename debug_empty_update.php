<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debug Empty/New Value Update Issue ===" . PHP_EOL;

// Test different scenarios
$cr = \App\Models\Change_request::find(31351);
if ($cr) {
    echo "CR 31351 found" . PHP_EOL;
    
    // Test 1: Update with empty string
    echo PHP_EOL . "Test 1: Update with empty string" . PHP_EOL;
    $result1 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
        ->where('custom_field_name', 'mds_approvers')
        ->update(['custom_field_value' => '']);
    
    echo "Empty update result: " . ($result1 ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    
    // Check after empty update
    $mdsField1 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
        ->where('custom_field_name', 'mds_approvers')
        ->first();
    echo "Value after empty update: '{$mdsField1->custom_field_value}'" . PHP_EOL;
    
    // Test 2: Update with new value
    echo PHP_EOL . "Test 2: Update with new value 'new.test.user'" . PHP_EOL;
    $result2 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
        ->where('custom_field_name', 'mds_approvers')
        ->update(['custom_field_value' => 'new.test.user']);
    
    echo "New value update result: " . ($result2 ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    
    // Check after new value update
    $mdsField2 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
        ->where('custom_field_name', 'mds_approvers')
        ->first();
    echo "Value after new update: '{$mdsField2->custom_field_value}'" . PHP_EOL;
    
    // Test 3: Check if there are any validation rules or observers
    echo PHP_EOL . "Test 3: Check ChangeRequestCustomField model" . PHP_EOL;
    $model = new \App\Models\ChangeRequestCustomField();
    
    // Check if there are any observers
    $observers = $model->getObservers();
    echo "Observers: " . json_encode($observers) . PHP_EOL;
    
    // Check if there are any events being dispatched
    echo PHP_EOL . "Check if field is being updated through service or directly" . PHP_EOL;
    
    // Reset to original value
    echo PHP_EOL . "Resetting to original value: 'test.user.002337'" . PHP_EOL;
    $result3 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
        ->where('custom_field_name', 'mds_approvers')
        ->update(['custom_field_value' => 'test.user.002337']);
    
    echo "Reset result: " . ($result3 ? 'SUCCESS' : 'FAILED') . PHP_EOL;
}
