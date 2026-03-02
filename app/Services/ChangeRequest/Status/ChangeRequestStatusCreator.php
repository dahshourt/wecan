<?php

namespace App\Services\ChangeRequest\Status;

use App\Events\ChangeRequestStatusUpdated;
use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;
use App\Models\Change_request_statuse as ChangeRequestStatus;
use App\Models\NewWorkFlow;
use App\Models\Status;
use App\Models\TechnicalCr;
use App\Services\ChangeRequest\Status\Strategies;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChangeRequestStatusCreator
{
    private const TECHNICAL_REVIEW_STATUS = 0;
    private const WORKFLOW_NORMAL = 1;
    private const ACTIVE_STATUS = '1';
    private const INACTIVE_STATUS = '0';
    private const COMPLETED_STATUS = '2';

    private $statusRepository;
    private $active_flag = '0';

    public function __construct()
    {
        $this->statusRepository = new ChangeRequestStatusRepository();
    }

    public function processStatusUpdate(ChangeRequestStatusContext $context): void
    {
        Log::info('ChangeRequestStatusCreator: processStatusUpdate START', [
            'cr_id' => $context->changeRequest->id,
            'old_status_id' => $context->statusData['old_status_id'],
            'new_status_id' => $context->statusData['new_status_id'],
            'workflow_id' => $context->workflow->id,
        ]);

        $this->active_flag = '0';

        $technicalTeamCounts = $this->getTechnicalTeamCounts($context->changeRequest->id, $context->statusData['old_status_id']);
        
        Log::info('ChangeRequestStatusCreator: Technical team counts', [
            'cr_id' => $context->changeRequest->id,
            'technical_counts' => $technicalTeamCounts,
        ]);

        $this->updateCurrentStatus($context, $technicalTeamCounts);
        
        Log::info('ChangeRequestStatusCreator: updateCurrentStatus completed', [
            'cr_id' => $context->changeRequest->id,
        ]);

        $this->createNewStatuses($context);
        
        Log::info('ChangeRequestStatusCreator: createNewStatuses completed', [
            'cr_id' => $context->changeRequest->id,
            'final_active_flag' => $this->active_flag,
        ]);
    }

    public function getActiveFlag(): string
    {
        return $this->active_flag;
    }

    private function getTechnicalTeamCounts(int $changeRequestId, int $oldStatusId): array
    {
        $technicalCr = TechnicalCr::where('cr_id', $changeRequestId)
            ->whereRaw('CAST(status AS CHAR) = ?', ['1'])
            ->first();

        if (!$technicalCr) {
            return ['total' => 0, 'approved' => 0];
        }

        $total = $technicalCr->technical_cr_team()
            ->where('current_status_id', $oldStatusId)
            ->count();

        $approved = $technicalCr->technical_cr_team()
            ->where('current_status_id', $oldStatusId)
            ->whereRaw('CAST(status AS CHAR) = ?', ['1'])
            ->count();

        return ['total' => $total, 'approved' => $approved];
    }

    private function updateCurrentStatus(
        ChangeRequestStatusContext $context,
        array $technicalTeamCounts
    ): void {
        // Use request() helper for reference_status check as it might simpler than parsing context->request 
        // effectively context->request should have it too.
        if (isset($context->request['reference_status']) || request()->reference_status) {
            $refStatus = $context->request['reference_status'] ?? request()->reference_status;
            $currentStatus = ChangeRequestStatus::find($refStatus);
        } else {
            $currentStatus = ChangeRequestStatus::where('cr_id', $context->changeRequest->id)
                ->where('new_status_id', $context->statusData['old_status_id'])
                ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
                ->first();
        }

        if (!$currentStatus) {
            Log::warning('Current status not found for update', [
                'cr_id' => $context->changeRequest->id,
                'old_status_id' => $context->statusData['old_status_id'],
            ]);
            return;
        }

        $workflowActive = $context->workflow->workflow_type == self::WORKFLOW_NORMAL
            ? self::INACTIVE_STATUS
            : self::COMPLETED_STATUS;

        $slaDifference = $this->calculateSlaDifference($currentStatus->created_at);
        $shouldUpdate = $this->shouldUpdateCurrentStatus($context->statusData['old_status_id'], $technicalTeamCounts);

        if ($shouldUpdate) {
            $currentStatus->update([
                'sla_dif' => $slaDifference,
                'active' => self::COMPLETED_STATUS
            ]);

            $this->handleDependentStatuses($context->changeRequest->id, $currentStatus, $workflowActive);
        }
    }

    private function shouldUpdateCurrentStatus(int $oldStatusId, array $technicalTeamCounts): bool
    {
        if ($oldStatusId != self::TECHNICAL_REVIEW_STATUS) {
            return true;
        }

        return $technicalTeamCounts['total'] > 0 &&
            $technicalTeamCounts['total'] == $technicalTeamCounts['approved'];
    }

    private function calculateSlaDifference(string $createdAt): int
    {
        return Carbon::parse($createdAt)->diffInDays(Carbon::now());
    }

    private function handleDependentStatuses(
        int $changeRequestId,
        ChangeRequestStatus $currentStatus,
        string $workflowActive
    ): void {
        $dependentStatuses = ChangeRequestStatus::where('cr_id', $changeRequestId)
            ->where('old_status_id', $currentStatus->old_status_id)
            ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->get();

        if (!$workflowActive) {
            $dependentStatuses->each(function ($status) {
                $status->update(['active' => self::INACTIVE_STATUS]);
            });
        }
    }

    private function createNewStatuses(ChangeRequestStatusContext $context): void
    {
        Log::info('ChangeRequestStatusCreator: createNewStatuses START', [
            'cr_id' => $context->changeRequest->id,
            'workflow_id' => $context->workflow->id,
            'workflow_statuses_count' => $context->workflow->workflowstatus->count(),
        ]);
        
        // Re-fetch current status logic if needed, or pass it down?
        // The original code re-fetched it inside createNewStatuses.
        $currentStatus = null;
        if (isset($context->request['reference_status']) || request()->reference_status) {
            $refStatus = $context->request['reference_status'] ?? request()->reference_status;
            $currentStatus = ChangeRequestStatus::find($refStatus);
            Log::info('ChangeRequestStatusCreator: Using reference_status', [
                'cr_id' => $context->changeRequest->id,
                'reference_status' => $refStatus,
                'current_status_found' => $currentStatus ? true : false,
            ]);
        } else {
            $currentStatus = ChangeRequestStatus::where('cr_id', $context->changeRequest->id)->where(
                'new_status_id',
                $context->statusData['old_status_id']
            )->first();
            Log::info('ChangeRequestStatusCreator: Looking up current status by old_status_id', [
                'cr_id' => $context->changeRequest->id,
                'old_status_id' => $context->statusData['old_status_id'],
                'current_status_found' => $currentStatus ? true : false,
            ]);
        }

        $strategy = $this->getWorkflowStrategy($context->changeRequest->workflow_type_id);
        
        Log::info('ChangeRequestStatusCreator: Strategy determined', [
            'cr_id' => $context->changeRequest->id,
            'workflow_type_id' => $context->changeRequest->workflow_type_id,
            'strategy_class' => get_class($strategy),
        ]);

        foreach ($context->workflow->workflowstatus as $index => $workflowStatus) {
            Log::info('ChangeRequestStatusCreator: Processing workflow status', [
                'cr_id' => $context->changeRequest->id,
                'workflow_status_index' => $index,
                'to_status_id' => $workflowStatus->to_status_id,
            ]);
            
            if ($strategy->shouldSkipWorkflowStatus($context->changeRequest, $workflowStatus, $context->statusData)) {
                Log::info('ChangeRequestStatusCreator: Workflow status skipped', [
                    'cr_id' => $context->changeRequest->id,
                    'to_status_id' => $workflowStatus->to_status_id,
                ]);
                continue;
            }

            $active = $strategy->determineActiveStatus(
                $context->changeRequest->id,
                $workflowStatus,
                $context->workflow,
                $context->statusData['old_status_id'],
                $context->statusData['new_status_id'],
                $context->changeRequest
            );
            $this->active_flag = $active;
            
            Log::info('ChangeRequestStatusCreator: Active status determined', [
                'cr_id' => $context->changeRequest->id,
                'to_status_id' => $workflowStatus->to_status_id,
                'determined_active' => $active,
            ]);

            $newStatusRow = Status::find($workflowStatus->to_status_id);
            $previous_group_id = session('current_group') ?: (auth()->check() ? auth()->user()->default_group : null);

            $viewTechFlag = $newStatusRow?->view_technical_team_flag ?? false;
            
            Log::info('ChangeRequestStatusCreator: Status row details', [
                'cr_id' => $context->changeRequest->id,
                'to_status_id' => $workflowStatus->to_status_id,
                'view_tech_flag' => $viewTechFlag,
                'previous_group_id' => $previous_group_id,
            ]);

            if ($viewTechFlag) {
                Log::info('ChangeRequestStatusCreator: Processing technical team workflow', [
                    'cr_id' => $context->changeRequest->id,
                    'to_status_id' => $workflowStatus->to_status_id,
                ]);
                
                $previous_technical_teams = [];
                if ($context->changeRequest && $context->changeRequest->technical_Cr_first) {
                    $previous_technical_teams = $context->changeRequest->technical_Cr_first->technical_cr_team
                        ? $context->changeRequest->technical_Cr_first->technical_cr_team->pluck('group_id')->toArray()
                        : [];
                }
                // Accessing request properties safely
                $reqTechnicalTeams = $context->request['technical_teams'] ?? $context->request->technical_teams ?? null;
                $teams = $reqTechnicalTeams ?? $previous_technical_teams;

                Log::info('ChangeRequestStatusCreator: Technical teams data', [
                    'cr_id' => $context->changeRequest->id,
                    'req_technical_teams' => $reqTechnicalTeams,
                    'previous_technical_teams' => $previous_technical_teams,
                    'final_teams' => $teams,
                ]);

                if (!empty($teams) && is_iterable($teams)) {
                    foreach ($teams as $teamGroupId) {
                        Log::info('ChangeRequestStatusCreator: Creating technical team status record', [
                            'cr_id' => $context->changeRequest->id,
                            'team_group_id' => $teamGroupId,
                        ]);
                        
                        $this->createStatusRecord(
                            $context->changeRequest->id,
                            $context->statusData['old_status_id'],
                            (int) $workflowStatus->to_status_id,
                            (int) $teamGroupId,
                            (int) $teamGroupId,
                            (int) $previous_group_id,
                            (int) $teamGroupId,
                            $context->userId,
                            $active
                        );
                    }
                } else {
                    Log::warning('ChangeRequestStatusCreator: No technical teams found for technical workflow', [
                        'cr_id' => $context->changeRequest->id,
                        'to_status_id' => $workflowStatus->to_status_id,
                    ]);
                }
            } else {
                Log::info('ChangeRequestStatusCreator: Processing normal workflow', [
                    'cr_id' => $context->changeRequest->id,
                    'to_status_id' => $workflowStatus->to_status_id,
                ]);
                
                $targetGroupId = optional($newStatusRow->group_statuses)
                    ->where('type', '2')
                    ->pluck('group_id')
                    ->first();

                $refGroupId = $currentStatus ? $currentStatus->reference_group_id : null;
                
                Log::info('ChangeRequestStatusCreator: Normal workflow group data', [
                    'cr_id' => $context->changeRequest->id,
                    'target_group_id' => $targetGroupId,
                    'ref_group_id' => $refGroupId,
                    'previous_group_id' => $previous_group_id,
                ]);

                $this->createStatusRecord(
                    $context->changeRequest->id,
                    $context->statusData['old_status_id'],
                    (int) $workflowStatus->to_status_id,
                    null,
                    $refGroupId,
                    $previous_group_id,
                    $targetGroupId,
                    $context->userId,
                    $active
                );
            }
        }
    }

    private function createStatusRecord($changeRequestId, $oldStatusId, $newStatusId, $groupId, $referenceGroupId, $previousGroupId, $currentGroupId, $userId, $active)
    {
        Log::info('ChangeRequestStatusCreator: createStatusRecord START', [
            'change_request_id' => $changeRequestId,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'group_id' => $groupId,
            'reference_group_id' => $referenceGroupId,
            'previous_group_id' => $previousGroupId,
            'current_group_id' => $currentGroupId,
            'user_id' => $userId,
            'active' => $active,
        ]);
        
        $payload = $this->buildStatusData(
            $changeRequestId,
            $oldStatusId,
            $newStatusId,
            $groupId,
            $referenceGroupId,
            $previousGroupId,
            $currentGroupId,
            $userId,
            $active
        );
        
        Log::info('ChangeRequestStatusCreator: Status payload built', [
            'change_request_id' => $changeRequestId,
            'payload' => $payload,
        ]);
        
        try {
            $result = $this->statusRepository->create($payload);
            
            if ($result) {
                Log::info('ChangeRequestStatusCreator: Status record created successfully', [
                    'change_request_id' => $changeRequestId,
                    'new_status_id' => $newStatusId,
                    'created_record_id' => $result->id,
                ]);
            } else {
                Log::error('ChangeRequestStatusCreator: Status repository create returned null/false', [
                    'change_request_id' => $changeRequestId,
                    'new_status_id' => $newStatusId,
                ]);
            }
        } catch (Exception $e) {
            Log::error('ChangeRequestStatusCreator: Exception in createStatusRecord', [
                'change_request_id' => $changeRequestId,
                'new_status_id' => $newStatusId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function buildStatusData(
        int $changeRequestId,
        int $oldStatusId,
        int $newStatusId,
        ?int $group_id,
        ?int $reference_group_id,
        ?int $previous_group_id,
        ?int $current_group_id,
        int $userId,
        string $active
    ): array {
        $status = Status::find($newStatusId);
        $sla = $status ? (int) $status->sla : 0;

        return [
            'cr_id' => $changeRequestId,
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'group_id' => $group_id,
            'reference_group_id' => $reference_group_id,
            'previous_group_id' => $previous_group_id,
            'current_group_id' => $current_group_id,
            'user_id' => $userId,
            'sla' => $sla,
            'active' => $active,
        ];
    }

    private function getWorkflowStrategy(int $workflowTypeId): Strategies\WorkflowStrategyInterface
    {
        if ($workflowTypeId == 9) {
            return new Strategies\PromoWorkflowStrategy();
        }

        if ($workflowTypeId == 5) {
            return new Strategies\VendorWorkflowStrategy();
        }

        return new Strategies\DefaultWorkflowStrategy();
    }
}
