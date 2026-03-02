<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\NewWorkFlow;

echo "=== CHECKING WORKFLOW RULES FOR CR 31351 ===" . PHP_EOL;

$cr = ChangeRequest::find(31351);
echo "CR ID: " . $cr->id . PHP_EOL;
echo "Current Status: " . ($cr->getCurrentStatus() ? $cr->getCurrentStatus()->new_status_id : 'None') . PHP_EOL;

// Check all possible workflows from current status
$currentStatusId = $cr->getCurrentStatus()->new_status_id;
echo PHP_EOL . "=== AVAILABLE WORKFLOWS FROM STATUS $currentStatusId ===" . PHP_EOL;

$workflows = NewWorkFlow::where('from_status_id', $currentStatusId)
    ->where('type_id', $cr->workflow_type_id)
    ->where('active', '1')
    ->with('workflowstatus')
    ->get();

foreach ($workflows as $workflow) {
    echo "Workflow ID: " . $workflow->id . PHP_EOL;
    echo "  To Status: " . $workflow->to_status_id . PHP_EOL;
    
    foreach ($workflow->workflowstatus as $status) {
        echo "    Workflow Status ID: " . $status->id . PHP_EOL;
        echo "    Final Status: " . $status->to_status_id . PHP_EOL;
        echo "    Dependencies: " . ($status->dependency_ids ? implode(', ', $status->dependency_ids) : 'None') . PHP_EOL;
        echo "    New Workflow ID: " . $status->new_workflow_id . PHP_EOL;
    }
    echo PHP_EOL;
}

// Check specifically for the desired transition
echo "=== CHECKING DESIRED TRANSITION ===" . PHP_EOL;
$desiredWorkflow = NewWorkFlow::where('from_status_id', 340) // IOT In progress
    ->where('to_status_id', 343) // Fix Defect-3rd Parties  
    ->where('type_id', 5)
    ->first();

if ($desiredWorkflow) {
    echo "Direct workflow found: ID " . $desiredWorkflow->id . PHP_EOL;
} else {
    echo "No direct workflow found - checking indirect paths..." . PHP_EOL;
    
    // Check if there's a workflow that redirects to Technical FB
    $redirectWorkflow = NewWorkFlow::where('from_status_id', 340)
        ->where('to_status_id', 108) // Technical FB
        ->where('type_id', 5)
        ->first();
    
    if ($redirectWorkflow) {
        echo "Found redirect workflow to Technical FB: ID " . $redirectWorkflow->id . PHP_EOL;
        echo "This explains why the transition goes to Technical FB instead!" . PHP_EOL;
    }
}
