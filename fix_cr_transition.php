<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;

echo "=== FIX CR 31351 STATUS TRANSITION ===" . PHP_EOL;

$crId = 31351;
$oldStatusId = 343; // Fix Defect-3rd Parties
$newStatusId = 340; // IOT In progress

try {
    // Start transaction
    DB::beginTransaction();
    
    echo "1. Deactivating current active statuses..." . PHP_EOL;
    
    // Mark all current active statuses as completed
    $currentActive = ChangeRequestStatus::where('cr_id', $crId)
        ->where('active', '1')
        ->get();
    
    foreach ($currentActive as $status) {
        echo "   - Deactivating status ID: " . $status->id . PHP_EOL;
        $status->update(['active' => '2']);
    }
    
    echo "2. Creating new 'IOT In progress' status..." . PHP_EOL;
    
    // Get the new status details
    $newStatus = Status::find($newStatusId);
    if (!$newStatus) {
        throw new Exception("IOT In progress status not found!");
    }
    
    // Create the new status record
    $newStatusRecord = new ChangeRequestStatus();
    $newStatusRecord->cr_id = $crId;
    $newStatusRecord->old_status_id = $oldStatusId;
    $newStatusRecord->new_status_id = $newStatusId;
    $newStatusRecord->group_id = null;
    $newStatusRecord->reference_group_id = null;
    $newStatusRecord->previous_group_id = null;
    $newStatusRecord->current_group_id = null;
    $newStatusRecord->user_id = 365; // Current user
    $newStatusRecord->sla = $newStatus->sla;
    $newStatusRecord->sla_dif = 0;
    $newStatusRecord->active = '1'; // Set as active
    $newStatusRecord->assignment_user_id = null;
    $newStatusRecord->created_at = now();
    $newStatusRecord->updated_at = null;
    
    $newStatusRecord->save();
    
    echo "   ✅ New status created with ID: " . $newStatusRecord->id . PHP_EOL;
    
    // Commit transaction
    DB::commit();
    
    echo "3. Verifying the fix..." . PHP_EOL;
    
    // Verify the current status
    $cr = \App\Models\Change_request::find($crId);
    $currentStatus = $cr->getCurrentStatus();
    
    if ($currentStatus && $currentStatus->new_status_id == $newStatusId) {
        echo "   ✅ SUCCESS: CR 31351 is now in 'IOT In progress' status!" . PHP_EOL;
        echo "   ✅ Status ID: " . $currentStatus->new_status_id . PHP_EOL;
        echo "   ✅ Active: " . $currentStatus->active . PHP_EOL;
        echo "   ✅ Created: " . $currentStatus->created_at . PHP_EOL;
    } else {
        echo "   ❌ ERROR: Fix verification failed!" . PHP_EOL;
        throw new Exception("Status verification failed");
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "🎉 CR 31351 status transition completed successfully!" . PHP_EOL;
echo "The CR is now properly in 'IOT In progress' status and can continue its workflow." . PHP_EOL;
