<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\ChangeRequestCustomField;
use App\Models\CustomField;

echo "=== CR 31351 cr_member Custom Field Analysis ===\n";

// Get the CR record
$cr = ChangeRequest::find(31351);
if (!$cr) {
    echo "CR 31351 not found!\n";
    exit;
}

echo "CR ID: {$cr->id}\n";
echo "CR Name: {$cr->name}\n";
echo "Created: {$cr->created_at}\n";
echo "Updated: {$cr->updated_at}\n\n";

// Check if cr_member field exists in custom_fields table
$crMemberField = CustomField::where('name', 'cr_member')->first();
if ($crMemberField) {
    echo "Found cr_member custom field:\n";
    echo "  ID: {$crMemberField->id}\n";
    echo "  Name: {$crMemberField->name}\n";
    echo "  Label: {$crMemberField->label}\n";
    echo "  Type: {$crMemberField->type}\n";
    echo "  Class: {$crMemberField->class}\n";
    echo "  Related Table: {$crMemberField->related_table}\n";
    echo "  Default Value: {$crMemberField->default_value}\n";
    echo "  Active: {$crMemberField->active}\n\n";
} else {
    echo "cr_member custom field NOT found in custom_fields table!\n\n";
}

// Check custom field values for CR 31351
$customFields = ChangeRequestCustomField::where('cr_id', 31351)->get();
echo "Custom fields for CR 31351:\n";
echo "Total custom field records: " . $customFields->count() . "\n\n";

foreach ($customFields as $cf) {
    $field = CustomField::find($cf->custom_field_id);
    echo "Custom Field ID: {$cf->custom_field_id}\n";
    echo "  Name: " . ($field ? $field->name : 'N/A') . "\n";
    echo "  Label: " . ($field ? $field->label : 'N/A') . "\n";
    echo "  Value: {$cf->custom_field_value}\n";
    echo "  Created: {$cf->created_at}\n";
    echo "  Updated: {$cf->updated_at}\n";
    echo "  ---\n";
}

// Specifically look for cr_member value
$crMemberValue = ChangeRequestCustomField::where('cr_id', 31351)
    ->whereHas('custom_field', function($query) {
        $query->where('name', 'cr_member');
    })
    ->first();

if ($crMemberValue) {
    echo "\n=== cr_member Specific Value ===\n";
    echo "cr_member value: '{$crMemberValue->custom_field_value}'\n";
    echo "Last updated: {$crMemberValue->updated_at}\n";
} else {
    echo "\n=== cr_member Specific Value ===\n";
    echo "cr_member value: NOT FOUND\n";
}

echo "\n=== Recent Custom Field Updates for CR 31351 ===\n";
$recentUpdates = ChangeRequestCustomField::where('cr_id', 31351)
    ->orderBy('updated_at', 'desc')
    ->limit(10)
    ->get();

foreach ($recentUpdates as $update) {
    $field = CustomField::find($update->custom_field_id);
    echo "Field: " . ($field ? $field->name : 'ID: ' . $update->custom_field_id) . "\n";
    echo "  Value: '{$update->custom_field_value}'\n";
    echo "  Updated: {$update->updated_at}\n";
    echo "  ---\n";
}
