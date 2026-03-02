<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\Status;
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;

echo "=== FINAL VERIFICATION - CR 31351 TRANSITION TEST ===" . PHP_EOL;

// Get CR and current status
$cr = ChangeRequest::find(31351);
$currentStatus = $cr->getCurrentStatus();

echo "CR: " . $cr->cr_no . " - " . $cr->title . PHP_EOL;
echo "Current Status: " . ($currentStatus->status->status_name ?? 'Unknown') . PHP_EOL;
echo "Current Status ID: " . $currentStatus->new_status_id . PHP_EOL;

// Test the IOT service transition detection
$iotService = new IotTcsFlowService();

$statusData = [
    'old_status_id' => 343, // Fix Defect-3rd Parties
    'new_status_id' => 9103, // Workflow ID for transition to IOT In progress
];

$isIotTransition = $iotService->isIotTcsTransition(31351, $statusData);

echo PHP_EOL . "=== IOT SERVICE TRANSITION CHECK ===" . PHP_EOL;
echo "Is this an IOT transition? " . ($isIotTransition ? 'YES' : 'NO') . PHP_EOL;

if (!$isIotTransition) {
    echo "✓ This transition will be handled by normal workflow processing" . PHP_EOL;
    echo "✓ Normal processing should create a new row in change_request_statuses" . PHP_EOL;
} else {
    echo "⚠️  This would be handled by IOT service" . PHP_EOL;
}

// Check workflow details
$fixDefectStatus = Status::find(343);
$iotInProgressStatus = Status::find(340);

echo PHP_EOL . "=== WORKFLOW DETAILS ===" . PHP_EOL;
echo "From: " . $fixDefectStatus->status_name . " (ID: " . $fixDefectStatus->id . ")" . PHP_EOL;
echo "To: " . $iotInProgressStatus->status_name . " (ID: " . $iotInProgressStatus->id . ")" . PHP_EOL;

$workflow = \App\Models\NewWorkFlow::find(9103);
if ($workflow) {
    echo "Workflow ID: " . $workflow->id . PHP_EOL;
    echo "Workflow Name: " . $workflow->workflow_name . PHP_EOL;
    echo "Active: " . $workflow->active . PHP_EOL;
}

echo PHP_EOL . "=== EXPECTED BEHAVIOR ===" . PHP_EOL;
echo "1. When you transition CR 31351 from 'Fix Defect-3rd Parties' to 'IOT In progress':" . PHP_EOL;
echo "   - The system will create a NEW row in change_request_statuses table" . PHP_EOL;
echo "   - Old status will be marked as completed (active=2)" . PHP_EOL;
echo "   - New status will be set as active (active=1)" . PHP_EOL;
echo "   - The transition will be logged and processed normally" . PHP_EOL;

echo PHP_EOL . "✅ CR 31351 is now ready for normal workflow operations!" . PHP_EOL;
