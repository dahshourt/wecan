<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\NewWorkFlow;
use App\Models\Status;
use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;

echo "=== DEBUGGING AUTOMATED CREATION PROCESS ===" . PHP_EOL;

// Simulate the exact same data that the automated process would use
$cr = ChangeRequest::find(31351);
$workflow = NewWorkFlow::with('workflowstatus')->find(9103);

// Build the context data that would be passed to createStatusRecord
$changeRequestId = 31351;
$oldStatusId = 343; // Fix Defect-3rd Parties
$newStatusId = 340; // IOT In progress
$groupId = null;
$referenceGroupId = null;
$previousGroupId = null;
$currentGroupId = null; // This might be the issue
$userId = 365;
$active = '1';

echo "Context Data:" . PHP_EOL;
echo "  CR ID: " . $changeRequestId . PHP_EOL;
echo "  Old Status ID: " . $oldStatusId . PHP_EOL;
echo "  New Status ID: " . $newStatusId . PHP_EOL;
echo "  Group ID: " . $groupId . PHP_EOL;
echo "  Reference Group ID: " . $referenceGroupId . PHP_EOL;
echo "  Previous Group ID: " . $previousGroupId . PHP_EOL;
echo "  Current Group ID: " . $currentGroupId . PHP_EOL;
echo "  User ID: " . $userId . PHP_EOL;
echo "  Active: " . $active . PHP_EOL;

// Build the payload exactly like the automated process does
$status = Status::find($newStatusId);
$sla = $status ? (int) $status->sla : 0;

$payload = [
    'cr_id' => $changeRequestId,
    'old_status_id' => $oldStatusId,
    'new_status_id' => $newStatusId,
    'group_id' => $groupId,
    'reference_group_id' => $referenceGroupId,
    'previous_group_id' => $previousGroupId,
    'current_group_id' => $currentGroupId,
    'user_id' => $userId,
    'sla' => $sla,
    'active' => $active,
];

echo PHP_EOL . "Payload that would be sent to repository:" . PHP_EOL;
print_r($payload);

echo PHP_EOL . "=== TESTING REPOSITORY CREATE METHOD ===" . PHP_EOL;

try {
    $repository = new ChangeRequestStatusRepository();
    echo "Calling repository->create()..." . PHP_EOL;
    
    $result = $repository->create($payload);
    
    if ($result) {
        echo "✓ Repository create succeeded!" . PHP_EOL;
        echo "Created record ID: " . $result->id . PHP_EOL;
        
        // Verify it was actually created
        $verify = ChangeRequestStatus::find($result->id);
        if ($verify) {
            echo "✓ Record verified in database" . PHP_EOL;
        } else {
            echo "✗ Record NOT found in database after creation!" . PHP_EOL;
        }
    } else {
        echo "✗ Repository create returned false/null" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "✗ Exception in repository create: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "=== TESTING DIRECT MODEL CREATE ===" . PHP_EOL;

try {
    echo "Calling ChangeRequestStatus::create() directly..." . PHP_EOL;
    
    $result2 = ChangeRequestStatus::create($payload);
    
    if ($result2) {
        echo "✓ Direct model create succeeded!" . PHP_EOL;
        echo "Created record ID: " . $result2->id . PHP_EOL;
    } else {
        echo "✗ Direct model create returned false/null" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "✗ Exception in direct model create: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
