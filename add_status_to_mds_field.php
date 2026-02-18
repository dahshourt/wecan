<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Add Status 291 to MDS Approvers Field ===" . PHP_EOL;

// Get current status info
$currentStatus = \App\Models\NewWorkFlow::find(291);
if ($currentStatus) {
    echo "Current Status 291:" . PHP_EOL;
    echo "- Name: {$currentStatus->name}" . PHP_EOL;
    echo "- Workflow Type ID: {$currentStatus->workflow_type_id}" . PHP_EOL;
}

// Get mds_approvers field
$mdsField = \App\Models\CustomField::where('name', 'mds_approvers')->first();
if ($mdsField) {
    echo PHP_EOL . "Adding status 291 to mds_approvers field configuration..." . PHP_EOL;
    
    // Add the new status configuration
    $newConfig = [
        'custom_field_id' => $mdsField->id,
        'wf_type_id' => 5, // Same workflow type as other configs
        'form_type' => 2, // Edit form
        'status_id' => 291, // Current status
        'enable' => 1,
        'sort' => 101, // After existing configs
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    $result = \App\Models\CustomFieldGroup::create($newConfig);
    
    if ($result) {
        echo "✅ SUCCESS: Status 291 added to mds_approvers field" . PHP_EOL;
        echo "New configuration ID: {$result->id}" . PHP_EOL;
        
        // Show all configurations now
        echo PHP_EOL . "All mds_approvers configurations:" . PHP_EOL;
        $allConfigs = \App\Models\CustomFieldGroup::where('custom_field_id', $mdsField->id)
            ->orderBy('sort')
            ->get();
        
        foreach ($allConfigs as $config) {
            echo "- Status {$config->status_id} (Sort: {$config->sort})" . PHP_EOL;
        }
    } else {
        echo "❌ FAILED: Could not add status 291" . PHP_EOL;
    }
}
