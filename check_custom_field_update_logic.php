<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChangeRequestCustomField;
use App\Models\CustomField;

echo "=== Custom Field Update Logic Analysis ===\n";

// Check if there are any custom field update methods that might be clearing cr_member
echo "Looking for cr_member custom field records in the database...\n";

// Check all cr_member records across all CRs
$allCrMemberRecords = ChangeRequestCustomField::whereHas('custom_field', function($query) {
    $query->where('name', 'cr_member');
})->get();

echo "Total cr_member custom field records in database: " . $allCrMemberRecords->count() . "\n\n";

if ($allCrMemberRecords->count() > 0) {
    foreach ($allCrMemberRecords as $record) {
        echo "CR ID: {$record->cr_id}\n";
        echo "  Value: '{$record->custom_field_value}'\n";
        echo "  Created: {$record->created_at}\n";
        echo "  Updated: {$record->updated_at}\n";
        echo "  ---\n";
    }
} else {
    echo "No cr_member custom field records found in the entire database!\n";
}

echo "\n=== Checking for recently deleted cr_member records ===\n";

// Check logs or other tables that might show deletion history
echo "Checking if cr_member field was recently deleted for CR 31351...\n";

// Look for any custom fields that were recently updated for CR 31351
$recentUpdates = ChangeRequestCustomField::where('cr_id', 31351)
    ->where('updated_at', '>=', '2026-02-01')
    ->orderBy('updated_at', 'desc')
    ->get();

echo "Recent custom field updates for CR 31351 since Feb 1, 2026:\n";
foreach ($recentUpdates as $update) {
    $field = CustomField::find($update->custom_field_id);
    echo "Field: " . ($field ? $field->name : 'ID: ' . $update->custom_field_id) . "\n";
    echo "  Value: '{$update->custom_field_value}'\n";
    echo "  Updated: {$update->updated_at}\n";
    echo "  ---\n";
}

echo "\n=== Checking cr_member field definition ===\n";
$crMemberField = CustomField::where('name', 'cr_member')->first();
if ($crMemberField) {
    echo "cr_member field exists and is active: {$crMemberField->active}\n";
    echo "Field type: {$crMemberField->type}\n";
    echo "Related table: {$crMemberField->related_table}\n";
} else {
    echo "cr_member field not found!\n";
}
