<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomFieldGroup;

echo "=== CONFIGURING CORRECT WORKFLOW FOR CR 31351 ===\n";

// Delete existing records for Workflow Type 5 + Status 319
$deletedCount = CustomFieldGroup::where('form_type', 2)
    ->where('wf_type_id', 5) // Vendor ID 5
    ->where('status_id', 319) // Planned
    ->delete();

echo "Deleted {$deletedCount} existing records for Vendor (ID 5) + Planned (ID 319)\n";

// Add new configuration with requester_department disabled
$newConfig = [
    [
        'form_type' => 2,
        'wf_type_id' => 5,
        'status_id' => 319,
        'custom_field_id' => 1, // Assuming requester_department has ID 1
        'sort' => 1,
        'enable' => 0, // DISABLED
        'active' => 1,
    ],
    // Add other fields as needed
    [
        'form_type' => 2,
        'wf_type_id' => 5,
        'status_id' => 319,
        'custom_field_id' => 2, // Example: another field
        'sort' => 2,
        'enable' => 1, // ENABLED
        'active' => 1,
    ],
];

$insertedCount = 0;
foreach ($newConfig as $config) {
    try {
        $record = CustomFieldGroup::create($config);
        if ($record) {
            $insertedCount++;
            echo "Created: Custom Field ID {$config['custom_field_id']}, Enable: {$config['enable']}\n";
        }
    } catch (Exception $e) {
        echo "Error creating Custom Field ID {$config['custom_field_id']}: " . $e->getMessage() . "\n";
    }
}

echo "\nInserted {$insertedCount} new records\n";
echo "\n=== MANUAL CONFIGURATION NEEDED ===\n";
echo "Please visit: http://localhost:8085/tms/index.php/groups/list/specialviewupdate\n";
echo "And configure:\n";
echo "- Workflow Type: Vendor (the one that corresponds to ID 5)\n";
echo "- Status: Planned\n";
echo "- Set requester_department to DISABLED\n";
echo "- Configure other fields as needed\n";
