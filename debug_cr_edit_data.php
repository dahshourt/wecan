<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request;
use App\Http\Repository\CustomField\CustomFieldGroupTypeRepository;
use App\Models\CustomFieldGroup;

echo "=== DEBUG CR EDIT DATA LOADING ===\n";

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
    
    // Simulate the exact same query that ChangeRequestService uses
    echo "\n=== SIMULATING ChangeRequestService QUERY ===\n";
    
    $customFieldRepo = new CustomFieldGroupTypeRepository();
    $CustomFields = $customFieldRepo->CustomFieldsByWorkFlowTypeAndStatus(
        $cr->workflow_type_id,      // 5
        2,                          // FORM_TYPE_EDIT
        $currentStatus->status->id  // 319
    );
    
    echo "Found " . $CustomFields->count() . " custom fields:\n";
    
    $requesterDeptFound = false;
    foreach ($CustomFields as $cf) {
        echo "- ID: {$cf->id}, Field: '{$cf->CustomField->name}', Enable: {$cf->enable}, Status ID: " . ($cf->status_id ?? 'NULL') . "\n";
        
        if ($cf->CustomField->name === 'requester_department') {
            $requesterDeptFound = true;
            echo "  *** FOUND requester_department: Enable = {$cf->enable} ***\n";
        }
    }
    
    if (!$requesterDeptFound) {
        echo "\n*** requester_department NOT FOUND in results! ***\n";
    }
    
    // Let's also check what the raw SQL query returns
    echo "\n=== RAW SQL CHECK ===\n";
    
    $rawResults = CustomFieldGroup::with('CustomField')
        ->where('wf_type_id', 5)
        ->where('form_type', 2)
        ->where('status_id', 319)
        ->orderBy('sort')
        ->get();
    
    echo "Raw query results (specific status only):\n";
    foreach ($rawResults as $cf) {
        echo "- ID: {$cf->id}, Field: '{$cf->CustomField->name}', Enable: {$cf->enable}\n";
    }
    
    // Check if the field is actually disabled in the database
    echo "\n=== DIRECT DATABASE CHECK ===\n";
    $requesterDeptConfig = CustomFieldGroup::with('CustomField')
        ->where('wf_type_id', 5)
        ->where('form_type', 2)
        ->where('status_id', 319)
        ->whereHas('CustomField', function($q) {
            $q->where('name', 'requester_department');
        })
        ->first();
    
    if ($requesterDeptConfig) {
        echo "Direct check - requester_department config:\n";
        echo "- Config ID: {$requesterDeptConfig->id}\n";
        echo "- Enable: {$requesterDeptConfig->enable}\n";
        echo "- Status ID: {$requesterDeptConfig->status_id}\n";
        echo "- Sort: {$requesterDeptConfig->sort}\n";
    } else {
        echo "Direct check - requester_department config NOT FOUND!\n";
    }
}
