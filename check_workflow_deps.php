<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Change_request as ChangeRequest;
use App\Models\NewWorkFlow;

echo "=== CHECKING WORKFLOW DEPENDENCIES FOR CR 31351 ===" . PHP_EOL;

$cr = ChangeRequest::find(31351);
$workflow = NewWorkFlow::where('from_status_id', 343)
    ->whereHas('workflowstatus', function($q) {
        $q->where('to_status_id', 340);
    })
    ->where('type_id', 5)
    ->first();

echo 'CR ID: ' . $cr->id . PHP_EOL;
echo 'Current Status: ' . ($cr->getCurrentStatus() ? $cr->getCurrentStatus()->new_status_id : 'None') . PHP_EOL;

if ($workflow && $workflow->workflowstatus->first()) {
    $firstStatus = $workflow->workflowstatus->first();
    echo 'Workflow ID: ' . $workflow->id . PHP_EOL;
    echo 'First Workflow Status ID: ' . $firstStatus->id . PHP_EOL;
    echo 'Dependency IDs: ' . ($firstStatus->dependency_ids ? implode(', ', $firstStatus->dependency_ids) : 'None') . PHP_EOL;
    
    if ($firstStatus->dependency_ids) {
        echo PHP_EOL . "=== CHECKING DEPENDENCIES ===" . PHP_EOL;
        foreach ($firstStatus->dependency_ids as $depId) {
            $depWorkflow = NewWorkFlow::find($depId);
            if ($depWorkflow) {
                echo "Dependency Workflow ID: $depId" . PHP_EOL;
                echo "  From Status: " . $depWorkflow->from_status_id . PHP_EOL;
                echo "  To Status: " . $depWorkflow->to_status_id . PHP_EOL;
                
                // Check if dependency is met
                $dependencyMet = \App\Models\Change_request_statuse::where('cr_id', 31351)
                    ->where('new_status_id', $depWorkflow->from_status_id)
                    ->where('old_status_id', $depWorkflow->previous_status_id)
                    ->where('active', '2') // Completed
                    ->exists();
                
                echo "  Met: " . ($dependencyMet ? 'YES' : 'NO') . PHP_EOL;
            } else {
                echo "Dependency Workflow ID: $depId - NOT FOUND" . PHP_EOL;
            }
            echo PHP_EOL;
        }
    }
} else {
    echo 'No workflow or workflow status found' . PHP_EOL;
}
