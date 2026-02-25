<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkFlowType;

echo "=== DETAILED VENDOR WORKFLOW ANALYSIS ===\n";

// Get all vendor workflows
$vendorWorkflows = WorkFlowType::where('name', 'like', '%vendor%')
    ->orWhere('name', 'like', '%Vendor%')
    ->get(['id', 'name', 'active', 'created_at', 'updated_at']);

echo "Found " . $vendorWorkflows->count() . " vendor workflows:\n\n";

foreach ($vendorWorkflows as $wf) {
    echo "ID: {$wf->id}\n";
    echo "Name: '{$wf->name}'\n";
    echo "Active: " . ($wf->active ? 'Yes' : 'No') . "\n";
    echo "Created: " . $wf->created_at . "\n";
    echo "Updated: " . $wf->updated_at . "\n";
    echo "-------------------\n";
}

// Check which one CR 31351 actually uses
echo "\n=== CR 31351 WORKFLOW TYPE ===\n";
$cr = \App\Models\Change_request::find(31351);
if ($cr) {
    echo "CR 31351 uses Workflow Type ID: " . $cr->workflow_type_id . "\n";
    
    $crWorkflow = WorkFlowType::find($cr->workflow_type_id);
    if ($crWorkflow) {
        echo "Workflow Name: '{$crWorkflow->name}'\n";
        echo "Workflow Active: " . ($crWorkflow->active ? 'Yes' : 'No') . "\n";
    }
}

// Check what's configured in custom_fields_groups_type
echo "\n=== CUSTOM FIELD CONFIGURATIONS ===\n";
use App\Models\CustomFieldGroup;

$configs = CustomFieldGroup::where('form_type', 2)
    ->where('status_id', 319) // Planned status
    ->whereHas('CustomField', function($q) {
        $q->where('name', 'requester_department');
    })
    ->with(['WorkFlowType' => function($q) {
        $q->select('id', 'name');
    }])
    ->get(['id', 'wf_type_id', 'status_id', 'custom_field_id', 'enable']);

echo "Custom field configurations for requester_department + Planned status:\n";
foreach ($configs as $config) {
    echo "Config ID: {$config->id}\n";
    echo "Workflow Type ID: {$config->wf_type_id}\n";
    echo "Workflow Name: " . ($config->WorkFlowType ? $config->WorkFlowType->name : 'NULL') . "\n";
    echo "Enable: {$config->enable}\n";
    echo "-------------------\n";
}

// Check all vendor workflow configurations
echo "\n=== ALL VENDOR WORKFLOW CONFIGURATIONS FOR PLANNED STATUS ===\n";
$vendorConfigs = CustomFieldGroup::where('form_type', 2)
    ->where('status_id', 319) // Planned status
    ->whereIn('wf_type_id', $vendorWorkflows->pluck('id'))
    ->with(['WorkFlowType' => function($q) {
        $q->select('id', 'name');
    }])
    ->get(['id', 'wf_type_id', 'custom_field_id', 'enable', 'sort']);

echo "Found " . $vendorConfigs->count() . " configurations:\n";
foreach ($vendorConfigs as $config) {
    echo "Config ID: {$config->id}, Workflow ID: {$config->wf_type_id}, Field ID: {$config->custom_field_id}, Enable: {$config->enable}, Sort: {$config->sort}\n";
    if ($config->WorkFlowType) {
        echo "  Workflow Name: '{$config->WorkFlowType->name}'\n";
    }
}
