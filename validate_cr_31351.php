<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Change_request as ChangeRequest;
use App\Models\Status;
use App\Models\NewWorkFlow;
use Illuminate\Support\Facades\DB;

echo "=== COMPREHENSIVE TEST FOR CR 31351 ===" . PHP_EOL;

$crId = 31351;
$expectedStatusId = 340; // IOT In progress
$expectedStatusName = 'IOT In progress';

$tests = [];
$passed = 0;
$total = 0;

function runTest($testName, $testFunction, &$tests, &$passed, &$total) {
    $total++;
    echo "TEST $total: $testName ... ";
    
    try {
        $result = $testFunction();
        if ($result) {
            echo "✅ PASS" . PHP_EOL;
            $passed++;
            $tests[] = ['name' => $testName, 'status' => 'PASS', 'message' => 'Test passed successfully'];
        } else {
            echo "❌ FAIL" . PHP_EOL;
            $tests[] = ['name' => $testName, 'status' => 'FAIL', 'message' => 'Test condition not met'];
        }
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . PHP_EOL;
        $tests[] = ['name' => $testName, 'status' => 'ERROR', 'message' => $e->getMessage()];
    }
}

// Test 1: CR exists
runTest("CR 31351 exists", function() use ($crId) {
    $cr = ChangeRequest::find($crId);
    return $cr !== null;
}, $tests, $passed, $total);

// Test 2: CR has current status
runTest("CR has current status", function() use ($crId) {
    $cr = ChangeRequest::find($crId);
    return $cr->getCurrentStatus() !== null;
}, $tests, $passed, $total);

// Test 3: Current status is IOT In progress
runTest("Current status is IOT In progress", function() use ($crId, $expectedStatusId) {
    $cr = ChangeRequest::find($crId);
    $currentStatus = $cr->getCurrentStatus();
    return $currentStatus && $currentStatus->new_status_id == $expectedStatusId;
}, $tests, $passed, $total);

// Test 4: Status is active
runTest("Status is active", function() use ($crId) {
    $cr = ChangeRequest::find($crId);
    $currentStatus = $cr->getCurrentStatus();
    return $currentStatus && $currentStatus->active == '1';
}, $tests, $passed, $total);

// Test 5: Only one active status exists
runTest("Only one active status", function() use ($crId) {
    $activeCount = ChangeRequestStatus::where('cr_id', $crId)
        ->where('active', '1')
        ->count();
    return $activeCount == 1;
}, $tests, $passed, $total);

// Test 6: Status record is recent
runTest("Status record is recent", function() use ($crId) {
    $cr = ChangeRequest::find($crId);
    $currentStatus = $cr->getCurrentStatus();
    if (!$currentStatus) return false;
    
    $createdAt = $currentStatus->created_at;
    $now = now();
    $diffInMinutes = $now->diffInMinutes($createdAt);
    
    return $diffInMinutes < 60; // Created within last hour
}, $tests, $passed, $total);

// Test 7: Workflow exists for transition
runTest("Workflow exists", function() use ($crId, $expectedStatusId) {
    $cr = ChangeRequest::find($crId);
    $currentStatus = $cr->getCurrentStatus();
    if (!$currentStatus) return false;
    
    $workflow = NewWorkFlow::where('from_status_id', $currentStatus->new_status_id)
        ->where('type_id', $cr->workflow_type_id)
        ->where('active', '1')
        ->first();
    
    return $workflow !== null;
}, $tests, $passed, $total);

// Test 8: Target status exists in system
runTest("Target status exists", function() use ($expectedStatusId) {
    $status = Status::find($expectedStatusId);
    return $status !== null && $status->status_name === 'IOT In progress';
}, $tests, $passed, $total);

// Test 9: No duplicate active IOT In progress statuses
runTest("No duplicate active statuses", function() use ($crId, $expectedStatusId) {
    $duplicateCount = ChangeRequestStatus::where('cr_id', $crId)
        ->where('new_status_id', $expectedStatusId)
        ->where('active', '1')
        ->count();
    return $duplicateCount == 1;
}, $tests, $passed, $total);

// Test 10: Database integrity check
runTest("Database integrity", function() use ($crId) {
    $cr = ChangeRequest::find($crId);
    $currentStatus = $cr->getCurrentStatus();
    
    if (!$currentStatus) return false;
    
    // Check if the status record points to valid data
    $validOldStatus = Status::find($currentStatus->old_status_id);
    $validNewStatus = Status::find($currentStatus->new_status_id);
    
    return $validOldStatus && $validNewStatus;
}, $tests, $passed, $total);

echo PHP_EOL . "=== TEST RESULTS ===" . PHP_EOL;
echo "Passed: $passed/$total tests" . PHP_EOL;
echo "Success Rate: " . round(($passed/$total) * 100, 1) . "%" . PHP_EOL;

if ($passed == $total) {
    echo "🎉 ALL TESTS PASSED! CR 31351 is working correctly." . PHP_EOL;
} else {
    echo "⚠️  Some tests failed. Review the issues below:" . PHP_EOL;
}

echo PHP_EOL . "=== DETAILED RESULTS ===" . PHP_EOL;
foreach ($tests as $test) {
    $icon = $test['status'] == 'PASS' ? '✅' : ($test['status'] == 'FAIL' ? '❌' : '⚠️');
    echo "$icon {$test['name']}: {$test['status']} - {$test['message']}" . PHP_EOL;
}

echo PHP_EOL . "=== CURRENT STATE SUMMARY ===" . PHP_EOL;
$cr = ChangeRequest::find($crId);
$currentStatus = $cr->getCurrentStatus();

if ($currentStatus) {
    echo "CR Number: " . $cr->cr_no . PHP_EOL;
    echo "Current Status: " . ($currentStatus->status ? $currentStatus->status->status_name : 'N/A') . PHP_EOL;
    echo "Status ID: " . $currentStatus->new_status_id . PHP_EOL;
    echo "Active: " . $currentStatus->active . PHP_EOL;
    echo "Record ID: " . $currentStatus->id . PHP_EOL;
    echo "Created: " . $currentStatus->created_at . PHP_EOL;
    echo "User ID: " . $currentStatus->user_id . PHP_EOL;
} else {
    echo "❌ No current status found!" . PHP_EOL;
}

echo PHP_EOL . "=== RECOMMENDATIONS ===" . PHP_EOL;
if ($passed == $total) {
    echo "✅ CR 31351 is ready for production use" . PHP_EOL;
    echo "✅ Interface transitions should work correctly" . PHP_EOL;
    echo "✅ Status is properly maintained" . PHP_EOL;
} else {
    echo "⚠️  Review failed tests and fix issues" . PHP_EOL;
    echo "⚠️  Run the fix script if needed: php solve_interface_issue.php" . PHP_EOL;
    echo "⚠️  Verify data integrity before proceeding" . PHP_EOL;
}
