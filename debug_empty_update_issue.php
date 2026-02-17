<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debug Empty Value Update Issue ===" . PHP_EOL;

// Test 1: Direct database update with empty string
echo "Test 1: Direct update with empty string" . PHP_EOL;
$result1 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
    ->where('custom_field_name', 'mds_approvers')
    ->update(['custom_field_value' => '']);

echo "Direct empty update result: " . ($result1 ? 'SUCCESS' : 'FAILED') . PHP_EOL;

// Check after empty update
$field1 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
    ->where('custom_field_name', 'mds_approvers')
    ->first();
echo "Value after empty update: '" . $field1->custom_field_value . "'" . PHP_EOL;

// Test 2: Direct database update with null
echo PHP_EOL . "Test 2: Direct update with null" . PHP_EOL;
$result2 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
    ->where('custom_field_name', 'mds_approvers')
    ->update(['custom_field_value' => null]);

echo "Direct null update result: " . ($result2 ? 'SUCCESS' : 'FAILED') . PHP_EOL;

// Check after null update
$field2 = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
    ->where('custom_field_name', 'mds_approvers')
    ->first();
echo "Value after null update: '" . ($field2->custom_field_value ?? 'NULL') . "'" . PHP_EOL;

// Test 3: Simulate the service method behavior
echo PHP_EOL . "Test 3: Simulating service updateCustomField method" . PHP_EOL;

// This simulates the updateCustomField method in ChangeRequestUpdateService
function simulateUpdateCustomField($crId, $fieldName, $fieldValue) {
    echo "simulateUpdateCustomField called with:" . PHP_EOL;
    echo "- crId: $crId" . PHP_EOL;
    echo "- fieldName: $fieldName" . PHP_EOL;
    echo "- fieldValue: " . var_export($fieldValue, true) . PHP_EOL;
    
    // This is the logic from the service
    if ($fieldValue === null) {
        echo "Skipping update because fieldValue is null" . PHP_EOL;
        return false;
    }
    
    echo "Proceeding with update" . PHP_EOL;
    return true;
}

// Test with empty string
simulateUpdateCustomField(31351, 'mds_approvers', '');

// Test with null
simulateUpdateCustomField(31351, 'mds_approvers', null);

// Test with actual value
simulateUpdateCustomField(31351, 'mds_approvers', 'test.value');

// Test 4: Check what the form actually sends for empty field
echo PHP_EOL . "Test 4: Checking what empty form field sends" . PHP_EOL;
echo "Empty string in PHP:" . PHP_EOL;
echo "- Length: " . strlen('') . PHP_EOL;
echo "- Is empty: " . (empty('') ? 'YES' : 'NO') . PHP_EOL;
echo "- Is null: " . (is_null('') ? 'YES' : 'NO') . PHP_EOL;
echo "- Equals null: " . (('' === null) ? 'YES' : 'NO') . PHP_EOL;

echo PHP_EOL . "Null in PHP:" . PHP_EOL;
echo "- Length: " . strlen(null) . PHP_EOL;
echo "- Is empty: " . (empty(null) ? 'YES' : 'NO') . PHP_EOL;
echo "- Is null: " . (is_null(null) ? 'YES' : 'NO') . PHP_EOL;
echo "- Equals null: " . ((null === null) ? 'YES' : 'NO') . PHP_EOL;
