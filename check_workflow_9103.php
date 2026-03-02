<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NewWorkFlow;
use App\Models\NewWorkFlowStatuses;

echo "=== Workflow 9103 Details ===" . PHP_EOL;
$workflow = NewWorkFlow::with('workflowstatus')->find(9103);
if ($workflow) {
    echo "Workflow ID: " . $workflow->id . PHP_EOL;
    echo "From Status ID: " . $workflow->from_status_id . PHP_EOL;
    echo "Type ID: " . $workflow->type_id . PHP_EOL;
    echo "Active: " . $workflow->active . PHP_EOL;
    echo "Same Time: " . $workflow->same_time . PHP_EOL;
    echo "To Status Label: " . $workflow->to_status_label . PHP_EOL;
    echo "Log Message: " . $workflow->log_message . PHP_EOL;
    
    echo PHP_EOL . "=== Workflow Statuses ===" . PHP_EOL;
    foreach ($workflow->workflowstatus as $ws) {
        echo "To Status ID: " . $ws->to_status_id . " | Order: " . $ws->order . PHP_EOL;
    }
} else {
    echo "Workflow 9103 not found" . PHP_EOL;
}

echo PHP_EOL . "=== All workflows from status 343 ===" . PHP_EOL;
$workflows = NewWorkFlow::with('workflowstatus')
    ->where('from_status_id', 343)
    ->where('active', '1')
    ->get();

foreach ($workflows as $wf) {
    echo "Workflow ID: " . $wf->id . " | Type ID: " . $wf->type_id . PHP_EOL;
    foreach ($wf->workflowstatus as $ws) {
        $toStatus = \App\Models\Status::find($ws->to_status_id);
        echo "  -> To: " . ($toStatus ? $toStatus->status_name : $ws->to_status_id) . " (ID: " . $ws->to_status_id . ")" . PHP_EOL;
    }
    echo PHP_EOL;
}
