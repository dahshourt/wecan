<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== FINAL FIX FOR CR 31351 STATUS TRANSITION ISSUE ===" . PHP_EOL;

$crId = 31351;
$oldStatusId = 343; // Fix Defect-3rd Parties
$newStatusId = 340; // IOT In progress

try {
    // Start transaction for data integrity
    DB::beginTransaction();
    
    echo "1. Validating CR and statuses..." . PHP_EOL;
    
    // Validate CR exists
    $cr = ChangeRequest::find($crId);
    if (!$cr) {
        throw new Exception("CR $crId not found");
    }
    
    // Validate target status exists
    $targetStatus = Status::find($newStatusId);
    if (!$targetStatus) {
        throw new Exception("Target status $newStatusId not found");
    }
    
    echo "   ✅ CR Found: " . $cr->cr_no . PHP_EOL;
    echo "   ✅ Target Status: " . $targetStatus->status_name . PHP_EOL;
    
    echo "2. Cleaning up existing statuses..." . PHP_EOL;
    
    // Mark ALL existing statuses as completed to avoid conflicts
    $updatedCount = ChangeRequestStatus::where('cr_id', $crId)
        ->where('active', '1')
        ->update(['active' => '2']);
    
    echo "   ✅ Marked $updatedCount existing active statuses as completed" . PHP_EOL;
    
    echo "3. Creating new status record..." . PHP_EOL;
    
    // Create the new status record directly (bypassing workflow service)
    $newStatusRecord = new ChangeRequestStatus();
    $newStatusRecord->cr_id = $crId;
    $newStatusRecord->old_status_id = $oldStatusId;
    $newStatusRecord->new_status_id = $newStatusId;
    $newStatusRecord->group_id = null;
    $newStatusRecord->reference_group_id = null;
    $newStatusRecord->previous_group_id = null;
    $newStatusRecord->active = '1';
    $newStatusRecord->created_at = now();
    $newStatusRecord->updated_at = now();
    
    // Get SLA from target status
    if ($targetStatus->sla) {
        $newStatusRecord->sla = $targetStatus->sla;
    }
    
    $newStatusRecord->save();
    
    echo "   ✅ New status record created: ID " . $newStatusRecord->id . PHP_EOL;
    echo "   ✅ Status: Fix Defect-3rd Parties -> IOT In progress" . PHP_EOL;
    echo "   ✅ Active flag set to 1" . PHP_EOL;
    
    if (isset($newStatusRecord->deadline)) {
        echo "   ✅ Deadline set: " . $newStatusRecord->deadline . PHP_EOL;
    }
    
    echo "4. Verifying the fix..." . PHP_EOL;
    
    // Refresh the CR model to get updated status
    $cr->refresh();
    $currentStatus = $cr->getCurrentStatus();
    
    if (!$currentStatus) {
        throw new Exception("Failed to retrieve current status after update");
    }
    
    if ($currentStatus->new_status_id != $newStatusId) {
        throw new Exception("Status mismatch: expected $newStatusId, got " . $currentStatus->new_status_id);
    }
    
    if ($currentStatus->active != '1') {
        throw new Exception("Active flag mismatch: expected 1, got " . $currentStatus->active);
    }
    
    echo "   ✅ Current Status ID: " . $currentStatus->new_status_id . PHP_EOL;
    echo "   ✅ Current Status Name: " . $currentStatus->status->status_name . PHP_EOL;
    echo "   ✅ Active Flag: " . $currentStatus->active . PHP_EOL;
    
    // Log the successful fix
    Log::info('CR 31351 status transition fixed manually', [
        'cr_id' => $crId,
        'old_status_id' => $oldStatusId,
        'new_status_id' => $newStatusId,
        'status_record_id' => $newStatusRecord->id,
        'method' => 'direct_database_fix'
    ]);
    
    // Commit the transaction
    DB::commit();
    
    echo PHP_EOL . "🎉 SUCCESS: CR 31351 status transition issue has been resolved!" . PHP_EOL;
    echo PHP_EOL . "=== SUMMARY ===" . PHP_EOL;
    echo "✅ CR is now in 'IOT In progress' status" . PHP_EOL;
    echo "✅ All previous status conflicts resolved" . PHP_EOL;
    echo "✅ Data integrity maintained with transaction" . PHP_EOL;
    echo "✅ Changes logged for audit purposes" . PHP_EOL;
    
    echo PHP_EOL . "=== NEXT STEPS ===" . PHP_EOL;
    echo "1. The interface transition should now work correctly" . PHP_EOL;
    echo "2. You can continue normal workflow operations" . PHP_EOL;
    echo "3. Monitor the CR to ensure no further automation conflicts" . PHP_EOL;
    
} catch (Exception $e) {
    // Roll back any changes if error occurs
    DB::rollBack();
    
    echo PHP_EOL . "❌ ERROR: Fix failed!" . PHP_EOL;
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    
    // Log the error
    Log::error('CR 31351 status transition fix failed', [
        'cr_id' => $crId,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    exit(1);
}

echo PHP_EOL . "=== VERIFICATION TEST ===" . PHP_EOL;
echo "Testing that the fix persists..." . PHP_EOL;

// Wait a moment and test again
sleep(1);
$testCr = ChangeRequest::find($crId);
$testStatus = $testCr->getCurrentStatus();

if ($testStatus && $testStatus->new_status_id == $newStatusId && $testStatus->active == '1') {
    echo "✅ Fix verified - status is stable" . PHP_EOL;
} else {
    echo "⚠️  Warning - status may not be stable" . PHP_EOL;
    echo "Current state: " . ($testStatus ? $testStatus->new_status_id : 'None') . PHP_EOL;
}

echo PHP_EOL . "🎉 CR 31351 is ready for production use!" . PHP_EOL;
