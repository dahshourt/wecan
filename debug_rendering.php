<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request;
use App\Http\Repository\CustomField\CustomFieldGroupTypeRepository;

echo "=== DEBUG FRONTEND RENDERING ===\n";

// Simulate what happens in the edit method
$cr = Change_request::find(31351);
if ($cr) {
    echo "CR ID: " . $cr->id . "\n";
    echo "Workflow Type ID: " . $cr->workflow_type_id . "\n";
    
    $currentStatus = $cr->getCurrentStatus();
    if ($currentStatus) {
        echo "Current Status ID: " . $currentStatus->status->id . "\n";
        echo "Current Status Name: " . $currentStatus->status->name . "\n";
    }
    
    // Get the custom fields exactly like the controller does
    $customFieldRepo = new CustomFieldGroupTypeRepository();
    $CustomFields = $customFieldRepo->CustomFieldsByWorkFlowTypeAndStatus(
        $cr->workflow_type_id,      // 5
        2,                          // FORM_TYPE_EDIT
        $currentStatus->status->id  // 319
    );
    
    echo "\n=== SIMULATING VIEW LOGIC ===\n";
    
    // Simulate the view logic from custom_fields.blade.php
    $enabledFields = $CustomFields->filter(function ($item) {
        return isset($item->enable) && $item->enable == 1;
    });

    $disabledFields = $CustomFields->filter(function ($item) {
        return !isset($item->enable) || $item->enable != 1;
    });
    
    echo "Enabled fields: " . $enabledFields->count() . "\n";
    echo "Disabled fields: " . $disabledFields->count() . "\n";
    
    echo "\n=== DISABLED FIELDS (Read-Only Section) ===\n";
    foreach ($disabledFields as $item) {
        echo "- Field: '{$item->CustomField->name}', Label: '{$item->CustomField->label}', Enable: {$item->enable}\n";
        
        if ($item->CustomField->name === 'requester_department') {
            echo "  *** This field should appear in READ-ONLY section ***\n";
            echo "  *** HTML select should have 'disabled' attribute ***\n";
        }
    }
    
    echo "\n=== ENABLED FIELDS (Action Data Section) ===\n";
    foreach ($enabledFields as $item) {
        echo "- Field: '{$item->CustomField->name}', Label: '{$item->CustomField->label}', Enable: {$item->enable}\n";
    }
    
    // Check if requester_department exists in the database for this CR
    echo "\n=== CR CUSTOM FIELD VALUES ===\n";
    $requesterDeptValue = $cr->change_request_custom_fields
        ->where('custom_field_name', 'requester_department')
        ->sortByDesc('id')
        ->first();
    
    if ($requesterDeptValue) {
        echo "requester_department value in CR: " . $requesterDeptValue->custom_field_value . "\n";
    } else {
        echo "requester_department value in CR: NOT SET\n";
    }
    
    echo "\n=== TROUBLESHOOTING CHECKLIST ===\n";
    echo "1. Check if 'requester_department' appears in 'Read-Only Data' section on the edit page\n";
    echo "2. If it appears but is not disabled, check browser developer tools for the 'disabled' attribute\n";
    echo "3. If it doesn't appear at all, check if there are any JavaScript errors on the page\n";
    echo "4. Try clearing browser cache and refreshing the page\n";
    echo "5. Check if there are any CSS rules hiding the disabled field\n";
}
