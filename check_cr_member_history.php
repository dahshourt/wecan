<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChangeRequestCustomField;
use App\Models\CustomField;
use App\Models\CrAssignee;

echo "=== CR 31351 cr_member Assignment History ===\n";

// Check CrAssignee table for cr_member assignments
$crMemberAssignments = CrAssignee::where('cr_id', 31351)
    ->where('role', 'cr_member')
    ->get();

echo "CrAssignee records for cr_member:\n";
echo "Total records: " . $crMemberAssignments->count() . "\n\n";

foreach ($crMemberAssignments as $assignment) {
    echo "Assignment ID: {$assignment->id}\n";
    echo "  User ID: {$assignment->user_id}\n";
    echo "  Role: {$assignment->role}\n";
    echo "  Created: {$assignment->created_at}\n";
    echo "  Updated: {$assignment->updated_at}\n";
    echo "  ---\n";
}

// Check all assignments for CR 31351
$allAssignments = CrAssignee::where('cr_id', 31351)->get();
echo "\nAll assignments for CR 31351:\n";
foreach ($allAssignments as $assignment) {
    echo "Role: {$assignment->role}, User ID: {$assignment->user_id}, Created: {$assignment->created_at}\n";
}

echo "\n=== Checking if cr_member was ever set as custom field ===\n";

// Look for any evidence that cr_member was previously set
$crMemberField = CustomField::where('name', 'cr_member')->first();
if ($crMemberField) {
    $previousCrMemberValue = ChangeRequestCustomField::where('cr_id', 31351)
        ->where('custom_field_id', $crMemberField->id)
        ->first();
    
    if ($previousCrMemberValue) {
        echo "Found existing cr_member custom field record:\n";
        echo "  Value: '{$previousCrMemberValue->custom_field_value}'\n";
        echo "  Created: {$previousCrMemberValue->created_at}\n";
        echo "  Updated: {$previousCrMemberValue->updated_at}\n";
    } else {
        echo "No cr_member custom field record found for CR 31351\n";
    }
}

echo "\n=== Recent CRs with cr_member for comparison ===\n";
$recentCrMembers = ChangeRequestCustomField::whereHas('custom_field', function($query) {
    $query->where('name', 'cr_member');
})
->where('created_at', '>=', '2026-01-01')
->orderBy('created_at', 'desc')
->limit(10)
->get();

foreach ($recentCrMembers as $crMember) {
    echo "CR ID: {$crMember->cr_id}, Value: '{$crMember->custom_field_value}', Created: {$crMember->created_at}\n";
}
