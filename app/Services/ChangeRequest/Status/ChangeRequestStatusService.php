<?php

namespace App\Services\ChangeRequest\Status;

use App\Services\ChangeRequest\CrDependencyService;
use App\Services\ChangeRequest\Status\ChangeRequestStatusContextFactory;
use App\Services\ChangeRequest\Status\ChangeRequestStatusCreator;
use App\Services\ChangeRequest\Status\ChangeRequestStatusValidator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChangeRequestStatusService
{
    private $validator;
    private $creator;
    private $contextFactory;
    private $eventService;
    private ?CrDependencyService $dependencyService = null;

    public function __construct(
        ChangeRequestStatusValidator $validator,
        ChangeRequestStatusCreator $creator,
        ChangeRequestStatusContextFactory $contextFactory,
        ChangeRequestEventService $eventService
    ) {
        $this->validator = $validator;
        $this->creator = $creator;
        $this->contextFactory = $contextFactory;
        $this->eventService = $eventService;
    }

    public function updateChangeRequestStatus(int $changeRequestId, $request): bool
    {
        Log::info('ChangeRequestStatusService: updateChangeRequestStatus METHOD START', [
            'changeRequestId' => $changeRequestId,
            'request_new_status_id' => $request->new_status_id ?? 'not_set',
        ]);
        
        try {
            DB::beginTransaction();

            // 1. Build Context
            $context = $this->contextFactory->build($changeRequestId, $request);

            Log::info('ChangeRequestStatusService: updateChangeRequestStatus', [
                'changeRequestId' => $changeRequestId,
                'statusData' => $context->statusData,
                'workflow' => $context->workflow,
                'changeRequest' => $context->changeRequest,
                'userId' => $context->userId,
            ]);

            if (!$context->workflow) {
                $newStatusId = $context->statusData['new_status_id'] ?? 'not set';
                throw new Exception("Workflow not found for status: {$newStatusId}");
            }

            // 2. Validate
            Log::info('ChangeRequestStatusService: About to validate status change', [
                'changeRequestId' => $changeRequestId,
                'statusData' => $context->statusData,
            ]);
            
            $statusChanged = $this->validator->validateStatusChange($context);
            
            Log::info('ChangeRequestStatusService: Validation result', [
                'changeRequestId' => $changeRequestId,
                'statusChanged' => $statusChanged,
            ]);
            
            if (!$statusChanged) {
                Log::info('ChangeRequestStatusService: Status not changed, returning early', [
                    'changeRequestId' => $changeRequestId,
                ]);
                DB::commit();
                return true;
            }

            // 3. Check for dependency hold
            if ($this->validator->isTransitionFromPendingCab($context->changeRequest, $context->statusData)) {
                $depService = $this->getDependencyService();
                if ($depService->shouldHoldCr($changeRequestId)) {
                    $depService->applyDependencyHold($changeRequestId);
                    Log::info('CR held due to unresolved dependencies', [
                        'cr_id' => $changeRequestId,
                        'cr_no' => $context->changeRequest->cr_no,
                    ]);
                    DB::commit();
                    return true;
                }
            }

            // 4. Process Status Update (Creation)
            Log::info('ChangeRequestStatusService: About to call creator->processStatusUpdate', [
                'changeRequestId' => $changeRequestId,
                'context' => [
                    'cr_id' => $context->changeRequest->id,
                    'old_status_id' => $context->statusData['old_status_id'] ?? 'not_set',
                    'new_status_id' => $context->statusData['new_status_id'] ?? 'not_set',
                    'workflow_id' => $context->workflow->id ?? 'not_set',
                ],
            ]);
            
            $this->creator->processStatusUpdate($context);
            
            Log::info('ChangeRequestStatusService: creator->processStatusUpdate completed', [
                'changeRequestId' => $changeRequestId,
            ]);

            // 5. Fire Events
            $this->eventService->fireStatusUpdated($context, $this->creator->getActiveFlag());

            DB::commit();

            // 6. Fire Delivered Event (Post-commit)
            $this->eventService->checkAndFireDeliveredEvent($context);

            return true;

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error updating change request status', [
                'change_request_id' => $changeRequestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function getDependencyService(): CrDependencyService
    {
        if (!$this->dependencyService) {
            $this->dependencyService = new CrDependencyService();
        }
        return $this->dependencyService;
    }
}
