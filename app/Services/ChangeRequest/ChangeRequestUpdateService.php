<?php

namespace App\Services\ChangeRequest;

use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;
use App\Http\Repository\Logs\LogRepository;
use App\Models\CabCr;
use App\Models\CabCrUser;
use App\Models\Change_request;
use App\Models\Change_request_statuse;
use App\Models\ChangeRequestCustomField;
use App\Models\CrAssignee;
use App\Models\CustomField;
use App\Models\NewWorkFlow;
use App\Models\TechnicalCr;
use App\Models\TechnicalCrTeam;
use App\Models\TechnicalCrTeamStatus;
use App\Models\User;
use App\Traits\ChangeRequest\ChangeRequestConstants;
use Auth;
use Illuminate\Support\Arr;
use App\Events\ChangeRequestUserAssignment;
use App\Models\ChangeRequest;
use App\Http\Repository\KPIs\KPIRepository;
use App\Services\ChangeRequest\CrDependencyService;
use Illuminate\Support\Facades\Log;


class ChangeRequestUpdateService
{
    use ChangeRequestConstants;

    private const ACTIVE_STATUS = '1';

    private const INACTIVE_STATUS = '0';

    private const COMPLETED_STATUS = '2';

    public static array $ACTIVE_STATUS_ARRAY = [self::ACTIVE_STATUS, 1];

    public static array $INACTIVE_STATUS_ARRAY = [self::INACTIVE_STATUS, 0];

    public static array $COMPLETED_STATUS_ARRAY = [self::COMPLETED_STATUS, 2];

    protected $logRepository;

    protected $statusRepository;

    protected $estimationService;

    protected $validationService;

    protected $statusService;

    private $changeRequest_old;

    public function __construct()
    {
        $this->logRepository = new LogRepository();
        $this->statusRepository = new ChangeRequestStatusRepository();
        $this->estimationService = new ChangeRequestEstimationService();
        $this->validationService = new ChangeRequestValidationService();
        $this->statusService = new ChangeRequestStatusService();
    }


    public function update($id, $request)
    {
        //$this->changeRequest_old = Change_request::find($id);
        $this->changeRequest_old = Change_request::with('kpis', 'changeRequestCustomFields', 'dependencies')->find($id);

        // 0)Link KPI if selected
        //dd($request->all());
        if (isset($request['kpi']) && $request['kpi']) {
            //dd($request['kpi'], $this->changeRequest_old->cr_no);
            $kpiRepo = new KPIRepository();
            $kpiResult = $kpiRepo->attachKpiToChangeRequest($request['kpi'], $this->changeRequest_old->cr_no);
            if (isset($kpiResult['success']) && !$kpiResult['success']) {
                return true;
            }
        }

        // 1) CAB CR gate
        if ($this->handleCabCrValidation($id, $request)) {
            return true;
        }

        // 2) Per-process validators
        if ($this->handleTechnicalTeamValidation($id, $request)) {
            try {
                $statusData = $this->statusService->extractStatusData($request);
                $changeRequest = Change_request::find($id);
                $uatPromoService = new \App\Services\ChangeRequest\SpecialFlows\UatPromoFlowService();
                $newActiveStatus = $uatPromoService->handlePendingUatuActivation($changeRequest->id, $statusData, $changeRequest->workflow_type_id);

                if ($newActiveStatus !== null) {
                    $this->active_flag = $newActiveStatus;
                    Log::info('Pending UAT (promo) active status updated by special flow', [
                        'cr_id' => $changeRequest->id,
                        'new_active' => $newActiveStatus
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Error in UatPromoFlowService', [
                    'cr_id' => $changeRequest->id,
                    'error' => $e->getMessage()
                ]);
            }
            return true;
        }

        // 3) Assignments
        $this->handleUserAssignments($id, $request);

        // handle CR Assignees {developer, tester, designer, cr_member}
        $this->handleCrAssignees($id, $request);

        // 4) CAB users (if any)
        $this->handleCabUsers($id, $request);

        // 5) Technical teams bootstrap (parallel streams)
        $this->handleTechnicalTeams($id, $request);

        // 6) Per-team technical statuses (non-blocking)
        // $this->handleTechnicalStatuses($id, $request);

        // 7) Estimations
        $this->handleEstimations($id, $request);

        // if ($this->shouldHandleCabApproval($request)) {
        //     $this->processCabApproval($id, $request);
        // }

        // 8) Update CR data (custom fields + main cols)
        $this->updateCRData($id, $request);

        // CR Dependencies (depend_on field - stored in cr_dependencies table not cr custom field)
        $this->handleDependOn($id, $request);

        // this to ensure that the cr has the correct cr_type and only one cr type (normal, depend on or relevant)
        $this->enforceCrTypeIntegrity($id, $request);

        // 9) Update assignment on current CR status row
        $this->updateStatusAssignments($id, $request);

        // 10) CR-level status move (main workflow)
        if (isset($request->new_status_id)) {
            $this->statusService->updateChangeRequestStatus($id, $request);
        }
        if (!isset($request->new_status_id)) {
            event(new ChangeRequestUserAssignment($this->changeRequest_old, $request));
        }

        // 12) Audit
        $this->logRepository->logCreate($id, $request, $this->changeRequest_old, 'update');

        return true;
    }

    public function updateTestableFlag($id, $request)
    {
        $this->changeRequest_old = Change_request::find($id);
        $this->updateCRData($id, $request);
        $this->logRepository->logCreate($id, $request, $this->changeRequest_old, 'update');

        return true;
    }

    public function updateTopManagementFlag($id, $request)
    {
        $this->changeRequest_old = Change_request::find($id);
        $this->updateCRData($id, $request);
        $this->logRepository->logCreate($id, $request, $this->changeRequest_old, 'update');

        return true;
    }

    public function addFeedback($id, $request)
    {
        $this->changeRequest_old = Change_request::find($id);
        $this->updateCRData($id, $request);
        $this->logRepository->logCreate($id, $request, $this->changeRequest_old, 'update');

        return true;
    }

    /* ======================================================================
     |                          CORE DATA UPDATE
     * ====================================================================== */
    public function updateCRData($id, $request)
    {
        $arr = Arr::only($request->all(), $this->getRequiredFields());
        $fileFields = ['technical_attachments', 'business_attachments', 'cap_users', 'technical_teams', 'depend_on'];
        $data = Arr::except($request->all(), array_merge(['_method'], $fileFields));

        $this->handleCustomFieldUpdates($id, $data);

        return Change_request::where('id', $id)->update($arr);
    }

    /**
     * Auto-mirror CR status to tech stream(s).
     * $scope: 'actor' (default) or 'all'
     */
    public function mirrorCrStatusToTechStreams(int $crId, int $toStatusId, ?string $note = null, string $scope = 'actor'): void
    {

        if ($scope === 'all') {
            $teams = TechnicalCrTeam::query()
                ->whereHas('technicalCr', fn($q) => $q->where('cr_id', $crId))
                ->get();
        } else { // actor
            $actorGroupId = session('default_group') ?: auth()->user()->default_group;
            if (!$actorGroupId) {
                return;
            }

            $teams = TechnicalCrTeam::query()
                ->whereHas('technicalCr', fn($q) => $q->where('cr_id', $crId))
                ->where('group_id', $actorGroupId)
                ->get();
        }
        foreach ($teams as $team) {
            $this->advanceTeamStream($team->id, $toStatusId, $note ?? 'auto: mirrored from CR status');
        }
    }

    // CR Assignees
    protected function handleCrAssignees($id, $request): void
    {
        $assignments = [
            'developer' => $request->developer_id ?? null,
            'tester' => $request->tester_id ?? null,
            'designer' => $request->designer_id ?? null,
            'cr_member' => $request->cr_member ?? null,
        ];

        foreach ($assignments as $role => $userId) {
            if (!empty($userId)) {
                CrAssignee::create([
                    'cr_id' => $id,
                    'role' => $role,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    /* ======================================================================
     |                          CAB CR VALIDATION
     * ====================================================================== */
    protected function handleCabCrValidation($id, $request): bool
    {
        if ($request->cab_cr_flag != '1') {
            return false;
        }

        //$user_id = Auth::user()->id;
        $user_id = $request->input('user_id') ?? Auth::user()->id;
        // $cabCr = CabCr::where("cr_id", $id)->where('status', '0')->first();
        $cabCr = CabCr::where('cr_id', $id)->whereRaw('CAST(status AS CHAR) = ?', ['0'])->first();
        $checkWorkflowType = NewWorkFlow::find($request->new_status_id)->workflow_type;

        unset($request['cab_cr_flag']);

        if ($checkWorkflowType) { // reject
            $cabCr->status = '2';
            $cabCr->save();
            $cabCr->cab_cr_user()->where('user_id', $user_id)->update(['status' => '2']);
        } else { // approve
            $cabCr->cab_cr_user()->where('user_id', $user_id)->update(['status' => '1']);

            $countAllUsers = $cabCr->cab_cr_user->count();
            $countApprovedUsers = $cabCr->cab_cr_user->where('status', '1')->count();

            if ($countAllUsers > $countApprovedUsers) {
                $this->updateCRData($id, $request);

                return true;
            }
            $cabCr->status = '1';
            $cabCr->save();

        }

        return false;
    }

    protected function handleTechnicalTeamValidation($id, $request): bool
    {
        return $this->validationService->handleTechnicalTeamValidation($id, $request);
    }

    /* ======================================================================
     |                          ASSIGNMENTS & CAB USERS
     * ====================================================================== */
    protected function handleUserAssignments($id, $request): void
    {
        $user = $request['assign_to'] ? User::find($request['assign_to']) : Auth::user();

        //        if ($this->needsAssignmentUpdate($request)) {
//            $request['assignment_user_id'] = $user->id;
//        }
    }

    protected function needsAssignmentUpdate($request): bool
    {
        return (isset($request['dev_estimation'])) ||
            (isset($request['testing_estimation'])) ||
            (isset($request['design_estimation'])) ||
            ($request['assign_to']) ||
            (isset($request['CR_estimation']));
    }

    protected function handleCabUsers($id, $request): void
    {
        if (empty($request->cap_users)) {
            return;
        }

        $record = CabCr::create([
            'cr_id' => $id,
            'status' => '0',
        ]);

        foreach ($request->cap_users as $userId) {
            CabCrUser::create([
                'user_id' => $userId,
                'cab_cr_id' => $record->id,
                'status' => '0',
            ]);
        }
    }

    /* ======================================================================
     |                          TECHNICAL TEAMS (BOOTSTRAP)
     * ====================================================================== */
    protected function handleTechnicalTeams($id, $request): void
    {
        $newStatusId = $request->new_status_id ?? null;
        $workflow = $newStatusId ? NewWorkFlow::find($newStatusId) : null;
        $promo_special_flow_ids = array_values(config('change_request.promo_special_flow_ids', []));

        if (empty($request->technical_teams)) {
            if ($workflow) {
                $new_status_id = $workflow && isset($workflow->workflowstatus[0])
                    ? $workflow->workflowstatus[0]->to_status_id : null;
                if (in_array($new_status_id, $promo_special_flow_ids, true)) {
                    $oldStatusId = $request->old_status_id ?? null;
                    $current_status_data = Change_request_statuse::where('cr_id', $id)->where('new_status_id', $oldStatusId)->active()->first();
                    $technicalCr = TechnicalCr::where('cr_id', $id)->whereRaw('CAST(status AS CHAR) = ?', ['0'])->first();
                    TechnicalCrTeam::create([
                        'group_id' => $current_status_data->reference_group_id,
                        'technical_cr_id' => $technicalCr->id,
                        'current_status_id' => $new_status_id,
                        'status' => '0',
                    ]);
                    $extraData = [
                        'technical_teams' => [$current_status_data->reference_group_id],
                    ];

                    $request->merge($extraData);
                }

                return;
            }

            return;
        }

        $record = TechnicalCr::create([
            'cr_id' => $id,
            'status' => '0',
        ]);

        foreach ($request->technical_teams as $groupId) {
            TechnicalCrTeam::create([
                'group_id' => $groupId,
                'technical_cr_id' => $record->id,
                'current_status_id' => $workflow && isset($workflow->workflowstatus[0])
                    ? $workflow->workflowstatus[0]->to_status_id
                    : null,
                'status' => '0',
            ]);
        }

        // 11) Auto-mirror CR status to tech stream(s) if no explicit tech params were sent
        if (isset($request->new_status_id)) {
            $new_status_id = $workflow && isset($workflow->workflowstatus[0])
                ? $workflow->workflowstatus[0]->to_status_id : null;
            // Scope 'actor': mirror only to the logged-in user's team on this CR.
            // Change to 'all' to mirror to all streams.
            $this->mirrorCrStatusToTechStreams($id, (int) $new_status_id, $request->tech_note ?? null, 'all');
        }

    }

    /* ======================================================================
     |                          ESTIMATION
     * ====================================================================== */
    protected function handleEstimations($id, $request): void
    {
        $changeRequest = Change_request::find($id);
        $user = $request['assign_to'] ? User::find($request['assign_to']) : Auth::user();

        if ($this->needsEstimationCalculation($request)) {
            $data = $this->estimationService->calculateEstimation($id, $changeRequest, $request, $user);
            $request->merge($data);
        }
    }

    protected function needsEstimationCalculation($request): bool
    {
        return (isset($request['CR_duration']) && $request['CR_duration'] != '') ||
            (isset($request['dev_estimation']) && $request['dev_estimation'] != '') ||
            (isset($request['design_estimation']) && $request['design_estimation'] != '') ||
            (isset($request['testing_estimation']) && $request['testing_estimation'] != '');
    }

    protected function handleCustomFieldUpdates($id, $data): void
    {
        // Handle custom_fields array if present
        if (isset($data['custom_fields']) && is_array($data['custom_fields'])) {
            foreach ($data['custom_fields'] as $key => $value) {
                $this->updateCustomField($id, $key, $value);
            }
        }

        // Handle direct fields (legacy support)
        foreach ($data as $key => $value) {
            if ($key === 'testable') {
                $testable = (string) $value === '1' ? 1 : 0;
                $this->updateCustomField($id, $key, $testable);
            } elseif (!in_array($key, ['_token', 'testable', 'custom_fields', 'cr'])) {
                $this->updateCustomField($id, $key, $value);
            }
        }
    }

    /**
     * Helper method to update a single custom field
     */
    // protected function updateCustomField($crId, $fieldName, $fieldValue): void
    // {
    //     $customFieldId = CustomField::where('name', $fieldName)->first();

    //     if ($customFieldId) {
    //         // Convert array values to JSON string
    //         $fieldValue = is_array($fieldValue) ? json_encode($fieldValue) : $fieldValue;

    //         $changeRequestCustomField = [
    //             'cr_id' => $crId,
    //             'custom_field_id' => $customFieldId->id,
    //             'custom_field_name' => $fieldName,
    //             'custom_field_value' => $fieldValue,
    //             'user_id' => auth()->id(),
    //         ];

    //         $this->insertOrUpdateChangeRequestCustomField($changeRequestCustomField);
    //     }
    // }
    protected function updateCustomField($crId, $fieldName, $fieldValue): void
    {
        // Skip if the field value is null
        if ($fieldValue === null) {
            return;
        }

        /*
        no need will be store on the dependencies table
        if ($fieldName === 'depend_on') {
            $this->syncCrDependencies($crId, $fieldValue);
        }
        */

        $customField = CustomField::where('name', $fieldName)->first();

        if ($customField) {
            // Convert array values to JSON string
            $fieldValue = is_array($fieldValue) ? json_encode($fieldValue) : $fieldValue;

            $changeRequestCustomField = [
                'cr_id' => $crId,
                'custom_field_id' => $customField->id,
                'custom_field_name' => $fieldName,
                'custom_field_value' => $fieldValue,
                'user_id' => auth()->id(),
            ];

            $this->insertOrUpdateChangeRequestCustomField($changeRequestCustomField);
        }
    }

    /**
     * Handle depend_on field - syncs CR dependencies to cr_dependencies table
     * Handles empty array to clear all dependencies
     */
    protected function handleDependOn(int $crId, $request): void
    {
        // Check if depend_on field was in the form via hidden marker field
        // This allows clearing dependencies when user deselects all options
        if ($request->has('depend_on_exists')) {
            $dependOnValues = $request->input('depend_on', []);

            // Ensure it's an array
            if (!is_array($dependOnValues)) {
                $dependOnValues = empty($dependOnValues) ? [] : [$dependOnValues];
            }

            $this->syncCrDependencies($crId, $dependOnValues);
        }
    }

    protected function syncCrDependencies(int $crId, $fieldValue): void
    {
        $dependsOnCrNos = [];
        if (is_array($fieldValue)) {
            $dependsOnCrNos = $fieldValue;
        } elseif (is_string($fieldValue) && !empty($fieldValue)) {
            $decoded = json_decode($fieldValue, true);
            $dependsOnCrNos = is_array($decoded) ? $decoded : [$fieldValue];
        }

        // Filter out empty values and convert to integers
        $dependsOnCrNos = array_filter(array_map('intval', $dependsOnCrNos));

        $dependencyService = new CrDependencyService();
        $dependencyService->syncDependencies($crId, $dependsOnCrNos);
    }


    // If cr_type is "Normal": Clear both depend_on and relevant fields
    // If cr_type is "Depend On": Clear relevant field (keep depend_on) and vice versa
    protected function enforceCrTypeIntegrity(int $crId, $request): void
    {
        // Get the cr_type value from the request
        $crTypeValue = $request->input('cr_type');

        if (!$crTypeValue) {
            return; // No cr_type submitted, skip enforcement
        }
        // Get the CrType name
        $crType = \App\Models\CrType::find($crTypeValue);
        $crTypeName = $crType ? $crType->name : null;
        if (!$crTypeName) {
            return;
        }
        switch ($crTypeName) {
            case 'Normal':
                // Clear both depend_on and relevant fields
                $this->clearDependencies($crId);
                $this->clearRelevantCustomField($crId);
                break;
            case 'Depend On':
                // Clear relevant field (keep depend_on)
                $this->clearRelevantCustomField($crId);
                break;
            case 'Relevant':
                // Clear depend_on field (keep relevant)
                $this->clearDependencies($crId);
                break;
        }
    }

    // Clear all CR dependencies (depend_on field) (already handled from the fronend no need for it)
    protected function clearDependencies(int $crId): void
    {
        $dependencyService = new CrDependencyService();
        $dependencyService->syncDependencies($crId, []);
    }

    // Clear the 'relevant' custom field value
    protected function clearRelevantCustomField(int $crId): void
    {
        ChangeRequestCustomField::where('cr_id', $crId)
            ->where('custom_field_name', 'relevant')
            ->update(['custom_field_value' => '[]']);
    }

    protected function insertOrUpdateChangeRequestCustomField(array $data): void
    {
        if (in_array($data['custom_field_name'], ['technical_feedback', 'business_feedback'])) {
            ChangeRequestCustomField::create([
                'cr_id' => $data['cr_id'],
                'custom_field_id' => $data['custom_field_id'],
                'custom_field_name' => $data['custom_field_name'],
                'custom_field_value' => $data['custom_field_value'],
                'user_id' => $data['user_id'],
            ]);
        } else {
            ChangeRequestCustomField::updateOrCreate(
                [
                    'cr_id' => $data['cr_id'],
                    'custom_field_id' => $data['custom_field_id'],
                    'custom_field_name' => $data['custom_field_name'],
                ],
                [
                    'custom_field_value' => $data['custom_field_value'],
                    'user_id' => $data['user_id'],
                ]
            );
        }
    }

    protected function updateStatusAssignments($id, $request): void
    {
        $oldStatusId = $request->old_status_id ?? null;

        if (isset($request->assignment_user_id) && $oldStatusId) {
            Change_request_statuse::where('cr_id', $id)
                ->where('new_status_id', $oldStatusId)
                // ->where('active', '1')
                // ->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
                ->active()
                ->update(['assignment_user_id' => $request->assignment_user_id]);
        }

        $memberFields = ['cr_member', 'rtm_member', 'assignment_user_id', 'tester_id', 'developer_id', 'designer_id'];
        foreach ($memberFields as $field) {
            if (isset($request->$field) && $oldStatusId) {
                Change_request_statuse::where('cr_id', $id)
                    ->where('new_status_id', $oldStatusId)
                    // ->where('active', '1')
                    // ->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
                    ->active()
                    ->update(['assignment_user_id' => $request->$field]);
            }
        }
    }

    /* ======================================================================
     |                  TECHNICAL STREAM STATUS HANDLERS (NON-BLOCKING)
     * ====================================================================== */

    /**
     * Handle parallel technical stream status updates.
     * Supports:
     *  - $request->tech_statuses[technical_cr_team_id] = to_status_id
     *  - $request->tech_to_status_id for actor's own team on this CR
     */
    protected function handleTechnicalStatuses($id, $request): void
    {
        // A) explicit per-team map
        if (isset($request->tech_statuses) && is_array($request->tech_statuses)) {
            $notes = is_array($request->tech_notes ?? null) ? $request->tech_notes : [];
            foreach ($request->tech_statuses as $teamId => $toStatusId) {
                $this->advanceTeamStream((int) $teamId, (int) $toStatusId, $notes[$teamId] ?? null);
            }
        }

        // B) implicit for actor's team (single hop)
        if (isset($request->tech_to_status_id)) {
            $actor = Auth::user();
            $actorGroupId = $actor->group_id ?? null;

            if ($actorGroupId) {
                $team = TechnicalCrTeam::query()
                    ->whereHas('technicalCr', fn($q) => $q->where('cr_id', $id))
                    ->where('group_id', $actorGroupId)
                    ->first();

                if ($team) {
                    $this->advanceTeamStream(
                        $team->id,
                        (int) $request->tech_to_status_id,
                        $request->tech_note ?? null
                    );
                }
            }
        }
    }

    /**
     * Advance a single technical stream (team) if the transition is allowed.
     * Writes TechnicalCrTeamStatus; model events or DB triggers will sync the snapshot.
     */
    protected function advanceTeamStream(int $technicalCrTeamId, int $toStatusId, ?string $note = null): void
    {

        $team = TechnicalCrTeam::find($technicalCrTeamId);
        if (!$team) {
            return;
        }
        $oldStatusId = request()->old_status_id ?? 0;
        $fromStatusId = (int) ($team->current_status_id ?? $oldStatusId);

        // Validate (from -> to) via workflow graph
        // if (!$this->isAllowedTeamTransition($fromStatusId, $toStatusId)) {
        //     return; // or throw new \RuntimeException('Transition not allowed for this stream.');
        // }

        TechnicalCrTeamStatus::create([
            'technical_cr_team_id' => $team->id,
            'old_status_id' => $fromStatusId ?: null,
            'new_status_id' => $toStatusId,
            'user_id' => Auth::id(),
            'note' => $note,
        ]);
    }

    /**
     * Check if a (from -> to) transition is allowed for a tech stream
     * using new_workflow/new_workflow_statuses edges.
     */
    protected function isAllowedTeamTransition(int $fromStatusId, int $toStatusId): bool
    {
        // If stream hasn't started yet, allow first hop (mirroring-friendly).
        if ($fromStatusId === 0) {
            return true;
        }

        return NewWorkFlow::query()
            ->where('from_status_id', $fromStatusId)
            // ->where('active', '1')
            // ->whereIN('active',self::$ACTIVE_STATUS_ARRAY)
            ->active()
            ->whereHas('workflowstatus', fn($q) => $q->where('to_status_id', $toStatusId))
            ->exists();
    }

    private function shouldHandleCabApproval($request): bool
    {
        return isset($request->cab_cr_flag) && $request->cab_cr_flag == '1';
    }

    private function processCabApproval($id, $request): bool
    {
        $userId = Auth::user()->id ?? $request->user_id;
        $cabCr = CabCr::where('cr_id', $id)->whereRaw('CAST(status AS CHAR) = ?', ['1'])->first();

        if (!$cabCr) {
            return false;
        }

        $checkWorkflowType = NewWorkFlow::find($request->new_status_id)->workflow_type;

        if ($checkWorkflowType) { // reject
            $cabCr->status = '2';
            $cabCr->save();
            $cabCr->cab_cr_user()->where('user_id', $userId)->update(['status' => '2']);
        } else { // approve
            $cabCr->cab_cr_user()->where('user_id', $userId)->update(['status' => '1']);

            $countAllUsers = $cabCr->cab_cr_user->count();
            $countApprovedUsers = $cabCr->cab_cr_user->whereRaw('CAST(status AS CHAR) = ?', ['1'])->count();

            if ($countAllUsers > $countApprovedUsers) {
                return true;
            }
            $cabCr->status = '1';
            $cabCr->save();

        }

        return false;
    }
}
