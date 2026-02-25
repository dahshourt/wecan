<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomFieldGroup;
use App\Models\Change_request;

// Test the fixed query
$cr = Change_request::find(31351);
if ($cr) {
    echo "Testing fixed query for CR 31351:\n";
    echo "Workflow Type ID: " . $cr->workflow_type_id . PHP_EOL;
    echo "Status ID: " . $cr->getCurrentStatus()->status->id . PHP_EOL;
    
    // Test the new query logic
    $workflow_type_id = $cr->workflow_type_id;
    $form_type = 2; // Edit form
    $status_id = $cr->getCurrentStatus()->status->id;
    
    echo "\n=== NEW QUERY RESULTS ===\n";
    
    // First, get custom fields specifically for this status
    $specificStatusFields = CustomFieldGroup::with('CustomField')
        ->where('wf_type_id', $workflow_type_id)
        ->where('form_type', $form_type)
        ->where('status_id', $status_id)
        ->orderBy('sort')
        ->get();

    echo "Specific status fields: " . $specificStatusFields->count() . PHP_EOL;
    foreach ($specificStatusFields as $cf) {
        echo "- ID: {$cf->id}, Field: {$cf->CustomField->name}, Enable: {$cf->enable}\n";
    }
    
    // Then, get custom fields with NULL status_id that don't exist in specific status
    $specificFieldIds = $specificStatusFields->pluck('custom_field_id')->toArray();
    
    $nullStatusFields = CustomFieldGroup::with('CustomField')
        ->where('wf_type_id', $workflow_type_id)
        ->where('form_type', $form_type)
        ->whereNull('status_id')
        ->whereNotIn('custom_field_id', $specificFieldIds)
        ->orderBy('sort')
        ->get();

    echo "\nNULL status fields: " . $nullStatusFields->count() . PHP_EOL;
    foreach ($nullStatusFields as $cf) {
        echo "- ID: {$cf->id}, Field: {$cf->CustomField->name}, Enable: {$cf->enable}\n";
    }
    
    // Combine and return results
    $result = $specificStatusFields->concat($nullStatusFields);
    
    echo "\n=== COMBINED RESULTS ===\n";
    echo "Total fields: " . $result->count() . PHP_EOL;
    foreach ($result as $cf) {
        echo "- ID: {$cf->id}, Field: {$cf->CustomField->name}, Enable: {$cf->enable}\n";
    }
    
    // Check specifically for requester_department
    $requesterDept = $result->where('CustomField.name', 'requester_department')->first();
    if ($requesterDept) {
        echo "\n=== requester_department field ===\n";
        echo "Found: YES\n";
        echo "Enable: " . $requesterDept->enable . PHP_EOL;
        echo "Status ID: " . ($requesterDept->status_id ?? 'NULL') . PHP_EOL;
    } else {
        echo "\n=== requester_department field ===\n";
        echo "Found: NO\n";
    }
}
