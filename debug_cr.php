<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomFieldGroup;
use App\Models\Change_request;

// Get CR 31351 details
$cr = Change_request::find(31351);
if ($cr) {
    echo "CR ID: " . $cr->id . PHP_EOL;
    echo "CR No: " . $cr->cr_no . PHP_EOL;
    echo "Workflow Type ID: " . $cr->workflow_type_id . PHP_EOL;
    
    $currentStatus = $cr->getCurrentStatus();
    if ($currentStatus) {
        echo "Current Status ID: " . $currentStatus->status->id . PHP_EOL;
        echo "Current Status Name: " . $currentStatus->status->name . PHP_EOL;
    }
    
    // Check custom fields for this workflow + status combination
    echo "\n=== Custom Fields for this CR ===\n";
    $customFields = CustomFieldGroup::with('CustomField')
        ->where('wf_type_id', $cr->workflow_type_id)
        ->where('form_type', 2) // Edit form
        ->where(function ($query) use ($currentStatus) {
            $query->where('status_id', $currentStatus->status->id)
                  ->orWhereNull('status_id');
        })
        ->orderBy('sort')
        ->get();
    
    echo "Found " . $customFields->count() . " custom fields\n";
    
    foreach ($customFields as $cf) {
        echo "- ID: {$cf->id}, Field: {$cf->CustomField->name}, Status ID: " . ($cf->status_id ?? 'NULL') . ", Enable: {$cf->enable}\n";
    }
    
    // Check specifically for vendor workflow + planned status
    echo "\n=== Checking Vendor + Planned combination ===\n";
    
    // Find vendor workflow type ID
    $vendorWorkflow = \App\Models\WorkFlowType::where('name', 'like', '%vendor%')->first();
    if ($vendorWorkflow) {
        echo "Vendor Workflow ID: " . $vendorWorkflow->id . PHP_EOL;
        
        // Find planned status ID
        $plannedStatus = \App\Models\Status::where('status_name', 'like', '%planned%')->first();
        if ($plannedStatus) {
            echo "Planned Status ID: " . $plannedStatus->id . PHP_EOL;
            
            $vendorPlannedFields = CustomFieldGroup::with('CustomField')
                ->where('wf_type_id', $vendorWorkflow->id)
                ->where('form_type', 2)
                ->where('status_id', $plannedStatus->id)
                ->orderBy('sort')
                ->get();
            
            echo "Found " . $vendorPlannedFields->count() . " custom fields for Vendor + Planned\n";
            
            foreach ($vendorPlannedFields as $cf) {
                echo "- ID: {$cf->id}, Field: {$cf->CustomField->name}, Enable: {$cf->enable}\n";
            }
        } else {
            echo "Planned status not found\n";
        }
    } else {
        echo "Vendor workflow not found\n";
    }
    
} else {
    echo "CR 31351 not found" . PHP_EOL;
}
