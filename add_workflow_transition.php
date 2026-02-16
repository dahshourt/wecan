<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ADDING WORKFLOW TRANSITION ===\n\n";

// Add a workflow transition from status 331 (Vendor Internal Test) to itself
// This will allow the Edit button to appear

$fromStatusId = 331; // Vendor Internal Test
$toStatusId = 331;   // Vendor Internal Test (self-transition)
$typeId = 5;         // Workflow Type ID from CR

// Check if transition already exists
$existing = \App\Models\NewWorkFlow::where('from_status_id', $fromStatusId)
    ->where('to_status_id', $toStatusId)
    ->where('type_id', $typeId)
    ->first();

if ($existing) {
    echo "Workflow transition already exists:\n";
    echo "- ID: {$existing->id}\n";
    echo "- From: {$existing->from_status_id}\n";
    echo "- To: {$existing->to_status_id}\n";
    echo "- Active: {$existing->active}\n";
} else {
    // Create the workflow transition
    $newTransition = \App\Models\NewWorkFlow::create([
        'from_status_id' => $fromStatusId,
        'to_status_id' => $toStatusId,
        'type_id' => $typeId,
        'active' => 1,
        'previous_status_id' => null,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ Created workflow transition:\n";
    echo "- ID: {$newTransition->id}\n";
    echo "- From: {$newTransition->from_status_id}\n";
    echo "- To: {$newTransition->to_status_id}\n";
    echo "- Type: {$newTransition->type_id}\n";
    echo "- Active: {$newTransition->active}\n";
}

// Verify the fix
echo "\n=== VERIFYING FIX ===\n";
$cr = \App\Models\Change_request::find(31351);
$setStatusCount = $cr->getSetStatus()->count();
echo "getSetStatus() count now: $setStatusCount\n";

if ($setStatusCount > 0) {
    echo "✅ SUCCESS! Edit button should now appear for CR 31351\n";
} else {
    echo "❌ Still no set status found. There might be another issue.\n";
}
