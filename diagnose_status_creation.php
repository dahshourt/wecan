<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Change_request as ChangeRequest;
use App\Models\Status;
use App\Models\NewWorkFlow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DIAGNOSING WHY NEW ROWS CAN'T BE ADDED TO CR 31351 ===" . PHP_EOL;

$crId = 31351;

echo "CR ID: $crId" . PHP_EOL;
echo PHP_EOL;

// 1. Check current state
echo "=== 1. CURRENT STATE ANALYSIS ===" . PHP_EOL;
$cr = ChangeRequest::find($crId);
if (!$cr) {
    echo "❌ CR not found!" . PHP_EOL;
    exit(1);
}

echo "CR Number: " . $cr->cr_no . PHP_EOL;
echo "CR Title: " . $cr->cr_title . PHP_EOL;
echo "Workflow Type ID: " . $cr->workflow_type_id . PHP_EOL;

$currentStatus = $cr->getCurrentStatus();
if ($currentStatus) {
    echo "Current Status: " . ($currentStatus->status ? $currentStatus->status->status_name : 'N/A') . PHP_EOL;
    echo "Current Status ID: " . $currentStatus->new_status_id . PHP_EOL;
    echo "Current Active: " . $currentStatus->active . PHP_EOL;
    echo "Current Record ID: " . $currentStatus->id . PHP_EOL;
} else {
    echo "❌ No current status found!" . PHP_EOL;
}

// 2. Check database table structure
echo PHP_EOL . "=== 2. DATABASE TABLE ANALYSIS ===" . PHP_EOL;
echo "Checking change_request_statuses table structure..." . PHP_EOL;

$columns = Schema::getColumnListing('change_request_statuses');
echo "Table columns: " . implode(', ', $columns) . PHP_EOL;

// Check for required columns
$requiredColumns = ['cr_id', 'old_status_id', 'new_status_id', 'active', 'created_at'];
$missingColumns = [];
foreach ($requiredColumns as $col) {
    if (!in_array($col, $columns)) {
        $missingColumns[] = $col;
    }
}

if (!empty($missingColumns)) {
    echo "❌ Missing required columns: " . implode(', ', $missingColumns) . PHP_EOL;
} else {
    echo "✅ All required columns present" . PHP_EOL;
}

// 3. Check recent attempts
echo PHP_EOL . "=== 3. RECENT CREATION ATTEMPTS ANALYSIS ===" . PHP_EOL;
$recentAttempts = ChangeRequestStatus::where('cr_id', $crId)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "Recent status changes (last 10):" . PHP_EOL;
foreach ($recentAttempts as $attempt) {
    $oldStatus = Status::find($attempt->old_status_id);
    $newStatus = Status::find($attempt->new_status_id);
    $status = $attempt->active == '1' ? 'ACTIVE' : 'COMPLETED';
    
    echo "  ID: " . $attempt->id . 
         " | " . ($oldStatus ? $oldStatus->status_name : 'N/A') . 
         " → " . ($newStatus ? $newStatus->status_name : 'N/A') . 
         " | $status" . 
         " | " . $attempt->created_at .
         PHP_EOL;
}

// 4. Test direct database insertion
echo PHP_EOL . "=== 4. DIRECT DATABASE INSERTION TEST ===" . PHP_EOL;
echo "Testing if we can directly insert a new record..." . PHP_EOL;

try {
    DB::beginTransaction();
    
    $testRecord = [
        'cr_id' => $crId,
        'old_status_id' => 340, // IOT In progress
        'new_status_id' => 343, // Fix Defect-3rd Parties
        'group_id' => null,
        'reference_group_id' => null,
        'previous_group_id' => null,
        'current_group_id' => null,
        'user_id' => 365,
        'sla' => 0,
        'sla_dif' => 0,
        'active' => '1',
        'assignment_user_id' => null,
        'created_at' => now(),
        'updated_at' => null,
    ];
    
    $insertedId = DB::table('change_request_statuses')->insertGetId($testRecord);
    echo "✅ Direct insertion successful! ID: $insertedId" . PHP_EOL;
    
    // Clean up - remove the test record
    DB::table('change_request_statuses')->where('id', $insertedId)->delete();
    echo "✅ Test record cleaned up" . PHP_EOL;
    
    DB::commit();
    echo "✅ Database insertion test passed" . PHP_EOL;
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Database insertion failed: " . $e->getMessage() . PHP_EOL;
}

// 5. Test model insertion
echo PHP_EOL . "=== 5. MODEL INSERTION TEST ===" . PHP_EOL;
echo "Testing if we can insert using Eloquent model..." . PHP_EOL;

try {
    DB::beginTransaction();
    
    $testModel = new ChangeRequestStatus();
    $testModel->cr_id = $crId;
    $testModel->old_status_id = 340;
    $testModel->new_status_id = 343;
    $testModel->group_id = null;
    $testModel->reference_group_id = null;
    $testModel->previous_group_id = null;
    $testModel->current_group_id = null;
    $testModel->user_id = 365;
    $testModel->sla = 0;
    $testModel->sla_dif = 0;
    $testModel->active = '1';
    $testModel->assignment_user_id = null;
    $testModel->created_at = now();
    $testModel->updated_at = null;
    
    $testModel->save();
    echo "✅ Model insertion successful! ID: " . $testModel->id . PHP_EOL;
    
    // Clean up
    $testModel->delete();
    echo "✅ Test model cleaned up" . PHP_EOL;
    
    DB::commit();
    echo "✅ Model insertion test passed" . PHP_EOL;
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Model insertion failed: " . $e->getMessage() . PHP_EOL;
}

// 6. Check for constraints or triggers
echo PHP_EOL . "=== 6. CONSTRAINTS AND TRIGGERS ANALYSIS ===" . PHP_EOL;
echo "Checking for database constraints..." . PHP_EOL;

try {
    $constraints = DB::select("SELECT 
        CONSTRAINT_NAME, 
        CONSTRAINT_TYPE,
        TABLE_NAME,
        COLUMN_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'change_request_statuses'");
    
    if (empty($constraints)) {
        echo "No constraints found" . PHP_EOL;
    } else {
        echo "Found constraints:" . PHP_EOL;
        foreach ($constraints as $constraint) {
            echo "  - " . $constraint->CONSTRAINT_NAME . 
                 " (" . $constraint->CONSTRAINT_TYPE . ")" . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "❌ Could not check constraints: " . $e->getMessage() . PHP_EOL;
}

// 7. Check repository method
echo PHP_EOL . "=== 7. REPOSITORY METHOD TEST ===" . PHP_EOL;
echo "Testing the ChangeRequestStatusRepository create method..." . PHP_EOL;

try {
    $repo = app(\App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository::class);
    
    $testData = [
        'cr_id' => $crId,
        'old_status_id' => 340,
        'new_status_id' => 343,
        'group_id' => null,
        'reference_group_id' => null,
        'previous_group_id' => null,
        'current_group_id' => null,
        'user_id' => 365,
        'sla' => 0,
        'sla_dif' => 0,
        'active' => '1',
        'assignment_user_id' => null,
    ];
    
    DB::beginTransaction();
    $result = $repo->create($testData);
    echo "✅ Repository method successful! ID: " . $result->id . PHP_EOL;
    
    // Clean up
    $result->delete();
    DB::commit();
    echo "✅ Repository test passed" . PHP_EOL;
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Repository method failed: " . $e->getMessage() . PHP_EOL;
}

// 8. Check active status count
echo PHP_EOL . "=== 8. ACTIVE STATUS ANALYSIS ===" . PHP_EOL;
$activeCount = ChangeRequestStatus::where('cr_id', $crId)
    ->where('active', '1')
    ->count();

echo "Current active status count: $activeCount" . PHP_EOL;

if ($activeCount == 0) {
    echo "⚠️  WARNING: No active statuses found!" . PHP_EOL;
    echo "This might be why new rows can't be added - the system expects" . PHP_EOL;
    echo "an active status to transition from." . PHP_EOL;
} elseif ($activeCount > 1) {
    echo "⚠️  WARNING: Multiple active statuses found!" . PHP_EOL;
    echo "This might cause conflicts in the transition logic." . PHP_EOL;
} else {
    echo "✅ Exactly one active status found (correct state)" . PHP_EOL;
}

// 9. Summary and recommendations
echo PHP_EOL . "=== 9. DIAGNOSIS SUMMARY ===" . PHP_EOL;
echo "Based on the tests above, here are the potential issues:" . PHP_EOL;
echo PHP_EOL;

echo "If database insertion tests passed:" . PHP_EOL;
echo "✅ Database is working correctly" . PHP_EOL;
echo "✅ Model insertion works" . PHP_EOL;
echo "✅ Repository method works" . PHP_EOL;
echo "❌ Issue is likely in the business logic/validation layer" . PHP_EOL;
echo PHP_EOL;

echo "If database insertion tests failed:" . PHP_EOL;
echo "❌ Database-level issue (constraints, permissions, etc.)" . PHP_EOL;
echo "❌ Need to check database configuration" . PHP_EOL;
echo PHP_EOL;

echo "Most likely causes:" . PHP_EOL;
echo "1. Validation logic in ChangeRequestStatusValidator" . PHP_EOL;
echo "2. Workflow dependency issues" . PHP_EOL;
echo "3. Missing active status to transition from" . PHP_EOL;
echo "4. Business rules preventing the transition" . PHP_EOL;
echo PHP_EOL;

echo "=== RECOMMENDATIONS ===" . PHP_EOL;
echo "1. If all tests passed, the issue is in the validation logic" . PHP_EOL;
echo "2. Check the ChangeRequestStatusValidator class" . PHP_EOL;
echo "3. Ensure there's an active status to transition from" . PHP_EOL;
echo "4. Verify workflow dependencies are met" . PHP_EOL;
echo "5. Use the manual fix script if needed: php solve_interface_issue.php" . PHP_EOL;
