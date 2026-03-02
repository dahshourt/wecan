<?php

namespace App\Services\ChangeRequest\Status;

use App\Models\Change_request as ChangeRequest;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\NewWorkFlow;
use Exception;
use Illuminate\Support\Facades\Log;

class ChangeRequestStatusValidator
{
    private static ?int $PENDING_CAB_STATUS_ID = null;

    public static function init()
    {
        self::$PENDING_CAB_STATUS_ID = \App\Services\StatusConfigService::getStatusId('pending_cab');
    }

    /**
     * Validate the status change
     */
    /**
     * Validate the status change
     */
    public function validateStatusChange(ChangeRequestStatusContext $context): bool
    {
        Log::info('ChangeRequestStatusValidator: validateStatusChange START', [
            'cr_id' => $context->changeRequest->id,
            'old_status_id' => $context->statusData['old_status_id'],
            'new_status_id' => $context->statusData['new_status_id'],
        ]);
        
        // 1. Check if status is actually changing
        if ($context->statusData['old_status_id'] == $context->statusData['new_status_id']) {
            Log::info('ChangeRequestStatusValidator: Status not changing, returning false', [
                'cr_id' => $context->changeRequest->id,
                'old_status_id' => $context->statusData['old_status_id'],
                'new_status_id' => $context->statusData['new_status_id'],
            ]);
            return false;
        }
        
        Log::info('ChangeRequestStatusValidator: Status is changing, checking dependencies', [
            'cr_id' => $context->changeRequest->id,
        ]);

        // 2. Check workflow dependencies
        $firstWorkflowStatus = $context->workflow->workflowstatus->first();
        Log::info('ChangeRequestStatusValidator: About to check workflow dependencies', [
            'cr_id' => $context->changeRequest->id,
            'workflow_id' => $context->workflow->id,
            'first_workflow_status' => $firstWorkflowStatus ? [
                'id' => $firstWorkflowStatus->id,
                'dependency_ids' => $firstWorkflowStatus->dependency_ids,
            ] : 'null',
        ]);
        
        if (!$this->checkWorkflowDependencies($context->changeRequest->id, $firstWorkflowStatus)) {
            Log::info('ChangeRequestStatusValidator: Workflow dependencies not met, returning false', [
                'cr_id' => $context->changeRequest->id,
            ]);
            return false;
        }
        
        Log::info('ChangeRequestStatusValidator: Validation passed, returning true', [
            'cr_id' => $context->changeRequest->id,
        ]);

        return true;
    }

    /**
     * Check workflow dependencies
     */
    public function checkWorkflowDependencies(int $changeRequestId, $workflowStatus): bool
    {
        Log::info('ChangeRequestStatusValidator: checkWorkflowDependencies START', [
            'change_request_id' => $changeRequestId,
            'workflow_status_id' => $workflowStatus ? $workflowStatus->id : 'null',
            'dependency_ids' => $workflowStatus ? $workflowStatus->dependency_ids : 'null',
        ]);
        
        if (!$workflowStatus || !$workflowStatus->dependency_ids) {
            Log::info('ChangeRequestStatusValidator: No dependencies to check, returning true', [
                'change_request_id' => $changeRequestId,
            ]);
            return true;
        }

        $dependencyIds = array_diff(
            $workflowStatus->dependency_ids,
            [$workflowStatus->new_workflow_id]
        );
        
        Log::info('ChangeRequestStatusValidator: Dependency IDs to check', [
            'change_request_id' => $changeRequestId,
            'dependency_ids' => $dependencyIds,
        ]);

        foreach ($dependencyIds as $workflowId) {
            Log::info('ChangeRequestStatusValidator: Checking dependency', [
                'change_request_id' => $changeRequestId,
                'dependency_workflow_id' => $workflowId,
            ]);
            
            if (!$this->isDependencyMet($changeRequestId, $workflowId)) {
                Log::info('ChangeRequestStatusValidator: Dependency not met', [
                    'change_request_id' => $changeRequestId,
                    'dependency_workflow_id' => $workflowId,
                ]);
                return false;
            }
        }
        
        Log::info('ChangeRequestStatusValidator: All dependencies met, returning true', [
            'change_request_id' => $changeRequestId,
        ]);

        return true;
    }

    /**
     * Check if a specific dependency is met
     */
    public function isDependencyMet(int $changeRequestId, int $workflowId): bool
    {
        $dependentWorkflow = NewWorkFlow::find($workflowId);

        if (!$dependentWorkflow) {
            return false;
        }

        return ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('new_status_id', $dependentWorkflow->from_status_id)
            ->where('old_status_id', $dependentWorkflow->previous_status_id)
            ->where('active', '2') // Completed
            ->exists();
    }

    public function isTransitionFromPendingCab(ChangeRequest $changeRequest, array $statusData): bool
    {
        if (self::$PENDING_CAB_STATUS_ID === null) {
            return false;
        }

        $workflow = NewWorkFlow::where('from_status_id', self::$PENDING_CAB_STATUS_ID)
            ->where('type_id', $changeRequest->workflow_type_id)
            ->where('workflow_type', '0') // Normal workflow (not reject)
            ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->first();

        if (!$workflow) {
            return false;
        }

        /*return isset($statusData['old_status_id']) &&
               (int)$statusData['old_status_id'] === self::$PENDING_CAB_STATUS_ID;*/
        return isset($statusData['new_status_id']) &&
            (int) $statusData['new_status_id'] === $workflow->id;
    }
}
