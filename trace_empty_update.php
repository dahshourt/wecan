<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Trace Empty Update Flow ===" . PHP_EOL;

// Set a known value first
echo "Step 1: Set initial value" . PHP_EOL;
\App\Models\ChangeRequestCustomField::where('cr_id', 31351)
    ->where('custom_field_name', 'mds_approvers')
    ->update(['custom_field_value' => 'initial.test.value']);

// Check initial value
$initial = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
    ->where('custom_field_name', 'mds_approvers')
    ->first();
echo "Initial value: '{$initial->custom_field_value}'" . PHP_EOL;

// Step 2: Simulate exact request data from form
echo PHP_EOL . "Step 2: Simulate form submission with empty value" . PHP_EOL;

// Create request exactly as it would come from form
$request = new \Illuminate\Http\Request();
$request->merge([
    'mds_approvers' => '', // Empty string as form would send
    'title' => 'Test Update',
    '_method' => 'PATCH'
]);

echo "Request mds_approvers value: '" . $request->input('mds_approvers') . "'" . PHP_EOL;
echo "Request mds_approvers is empty: " . (empty($request->input('mds_approvers')) ? 'YES' : 'NO') . PHP_EOL;
echo "Request mds_approvers is null: " . (is_null($request->input('mds_approvers')) ? 'YES' : 'NO') . PHP_EOL;

// Step 3: Test the exact service method
echo PHP_EOL . "Step 3: Test handleCustomFieldUpdates method" . PHP_EOL;

// Get the service class
$service = new \App\Services\ChangeRequest\ChangeRequestUpdateService();

// Use reflection to call protected method for testing
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('handleCustomFieldUpdates');
$method->setAccessible(true);

// Call the method with our test data
$data = $request->all();
echo "Data passed to handleCustomFieldUpdates:" . PHP_EOL;
echo "- mds_approvers: '" . ($data['mds_approvers'] ?? 'NOT_SET') . "'" . PHP_EOL;
echo "- Is empty: " . (empty($data['mds_approvers']) ? 'YES' : 'NO') . PHP_EOL;
echo "- Is null: " . (is_null($data['mds_approvers']) ? 'YES' : 'NO') . PHP_EOL;

// Call the actual method
$method->invoke($service, 31351, $data);

// Check final result
echo PHP_EOL . "Step 4: Check final result" . PHP_EOL;
$final = \App\Models\ChangeRequestCustomField::where('cr_id', 31351)
    ->where('custom_field_name', 'mds_approvers')
    ->first();
echo "Final value: '{$final->custom_field_value}'" . PHP_EOL;

if ($final->custom_field_value === '') {
    echo "✅ EMPTY UPDATE SUCCESSFUL!" . PHP_EOL;
} else {
    echo "❌ EMPTY UPDATE FAILED!" . PHP_EOL;
}
