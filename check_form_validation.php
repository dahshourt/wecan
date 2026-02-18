<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Check Form Validation for Empty Values ===" . PHP_EOL;

// Test the actual validation that would be applied
echo "Test 1: Current validation rules for mds_approvers" . PHP_EOL;

// Get the current status for CR 31351
$cr = \App\Models\Change_request::find(31351);
$currentStatus = \App\Models\Change_request_statuse::where('cr_id', 31351)
    ->where('active', 1)
    ->first();

if ($currentStatus) {
    echo "Current status ID: {$currentStatus->new_status_id}" . PHP_EOL;
    
    // Get form fields for this status
    $formFields = new \App\Http\Repository\CustomField\CustomFieldGroupTypeRepository();
    $fields = $formFields->CustomFieldsByWorkFlowTypeAndStatus($cr->workflow_type_id, 2, $currentStatus->new_status_id);
    
    echo "Form fields for current status:" . PHP_EOL;
    foreach ($fields as $field) {
        if ($field->CustomField->name === 'mds_approvers') {
            echo "- Found mds_approvers field:" . PHP_EOL;
            echo "  - Validation type ID: {$field->validation_type_id}" . PHP_EOL;
            echo "  - Enable: " . ($field->enable ? 'Yes' : 'No') . PHP_EOL;
            echo "  - Status ID: " . ($field->status_id ?? 'NULL') . PHP_EOL;
            
            // Check if this field would have validation rules
            if ($field->validation_type_id == 1 && $field->enable == 1) {
                echo "  - ✅ Field has validation rules" . PHP_EOL;
            } else {
                echo "  - ❌ Field has NO validation rules" . PHP_EOL;
            }
        }
    }
}

// Test 2: Simulate actual form validation
echo PHP_EOL . "Test 2: Simulate form validation with empty value" . PHP_EOL;

// Create mock request
$requestData = [
    'mds_approvers' => '',
    'title' => 'Test',
    '_method' => 'PATCH',
    'old_status_id' => $currentStatus->new_status_id ?? null
];

// Test validation rules
$validator = \Illuminate\Support\Facades\Validator::make($requestData, [
    'mds_approvers' => ['sometimes', 'string']
]);

echo "Validation result with empty value:" . PHP_EOL;
if ($validator->fails()) {
    echo "- ❌ Validation failed: " . $validator->errors()->first() . PHP_EOL;
} else {
    echo "- ✅ Validation passed" . PHP_EOL;
}

// Test 3: Check if there are any middleware or observers
echo PHP_EOL . "Test 3: Check for any middleware that might strip empty values" . PHP_EOL;

// Check if there are any global middleware that might affect input
$middleware = app('router')->getMiddleware();
echo "Global middleware count: " . count($middleware) . PHP_EOL;

// Check if there are any request macros or filters
echo "Checking for request macros..." . PHP_EOL;
$macros = get_class_methods(\Illuminate\Http\Request::class);
$relevantMacros = array_filter($macros, function($method) {
    return strpos(strtolower($method), 'clean') !== false || 
           strpos(strtolower($method), 'strip') !== false ||
           strpos(strtolower($method), 'filter') !== false;
});

if (!empty($relevantMacros)) {
    echo "Found potentially relevant request macros:" . PHP_EOL;
    foreach ($relevantMacros as $macro) {
        echo "- $macro" . PHP_EOL;
    }
} else {
    echo "No relevant request macros found" . PHP_EOL;
}
