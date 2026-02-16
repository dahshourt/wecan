@extends('layouts.app')

@section('content')
@php
    $allStatuses = \App\Models\ReleaseStatus::getOrdered();
    $currentStatusId = $row->release_status_id ?? null;
    $currentStatus = $allStatuses->firstWhere('id', $currentStatusId);
    $currentOrder = $currentStatus ? $currentStatus->display_order : 0;
    $currentStatusName = $currentStatus->name ?? 'N/A';

    $changeRequests = $row->changeRequests ?? collect();
    $teamMembers = $row->teamMembers ?? collect();
    $stakeholders = $row->stakeholders ?? collect();
    $risks = $row->risks ?? collect();
    $attachments = $row->attachments ?? collect();
    $crAttachments = $row->crAttachments ?? collect();
    $feedbacks = $row->feedbacks ?? collect();
    $releaseLogs = $logs ?? collect();
@endphp

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <h2 class="text-white font-weight-bold my-2 mr-3">Release Details</h2>
                        <span class="label label-light-primary label-inline font-weight-bold">#{{ $row->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-custom gutter-b shadow-sm">
                <div class="card-body py-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50 symbol-circle symbol-light-primary mr-4 flex-shrink-0">
                                <span class="symbol-label font-size-h5 font-weight-bolder">R#{{ $row->id }}</span>
                            </div>
                            <div>
                                <h3 class="font-weight-bolder text-dark mb-0" style="font-size: 1.4rem;">{{ $row->name }}</h3>
                                <div class="d-flex align-items-center mt-1">
                                    <span class="label label-{{ $currentStatus->color ?? 'primary' }} label-inline font-weight-bold mr-2">
                                        {{ $currentStatusName }}
                                    </span>
                                    <span class="text-muted font-size-xs">
                                        <i class="la la-calendar-alt mr-1"></i>
                                        Created {{ $row->created_at ? $row->created_at->format('d M Y') : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            @can('Edit Release')
                            <a href="{{ route('releases.edit', $row->id) }}" class="btn btn-primary btn-sm font-weight-bolder mr-2">
                                <i class="la la-edit"></i> Edit
                            </a>
                            @endcan
                            <a href="{{ route('releases.index') }}" class="btn btn-light-primary btn-sm font-weight-bolder">
                                <i class="la la-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    {{-- Status Progress Stepper --}}
                    <div class="release-progress-stepper">
                        <div class="stepper-track"></div>
                        <div class="stepper-steps d-flex justify-content-between">
                            @foreach($allStatuses as $index => $status)
                                @php
                                    $isPassed = $status->display_order < $currentOrder;
                                    $isActive = $currentStatusId == $status->id;
                                    $isUpcoming = $status->display_order > $currentOrder;

                                    if ($isPassed) { $dotColor = '#1BC5BD'; $textClass = 'text-success'; }
                                    elseif ($isActive) { $dotColor = '#8c08aa'; $textClass = 'text-primary font-weight-bolder'; }
                                    else { $dotColor = '#E4E6EF'; $textClass = 'text-muted'; }
                                @endphp
                                <div class="stepper-step text-center" style="flex: 1; position: relative; z-index: 1;">
                                    <div class="mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center {{ $isActive ? 'stepper-dot-active' : '' }}"
                                         style="width: 26px; height: 26px; background: {{ $dotColor }}; transition: all 0.3s;">
                                        @if($isPassed)
                                            <i class="la la-check text-white" style="font-size: 13px;"></i>
                                        @elseif($isActive)
                                            <div class="rounded-circle bg-white" style="width: 8px; height: 8px;"></div>
                                        @endif
                                    </div>
                                    <span class="font-size-xs {{ $textClass }}" style="display: block; line-height: 1.2;">
                                        {{ $status->name }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 1: RELEASE INFORMATION --}}
            
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-primary py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-info-circle text-primary mr-2"></i>Release Information
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="text-muted font-size-sm">Release ID #</label>
                            <div class="font-weight-bolder font-size-lg text-primary">#{{ $row->id }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted font-size-sm">Release Name</label>
                            <div class="font-weight-bolder font-size-lg">{{ $row->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted font-size-sm">Vendor Name</label>
                            <div class="font-weight-bolder font-size-lg">{{ $row->vendor->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted font-size-sm">Target System</label>
                            <div class="font-weight-bolder">{{ $row->targetSystem->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted font-size-sm">Creator RTM Name</label>
                            <div class="font-weight-bolder">{{ $row->creator_rtm_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted font-size-sm">RTM Email</label>
                            <div class="font-weight-bolder">
                                @if($row->rtm_email)
                                    <a href="mailto:{{ $row->rtm_email }}">{{ $row->rtm_email }}</a>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: PLANNING DETAILS --}}
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-info py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-calendar-alt text-info mr-2"></i>Planning Details
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group mb-5">
                        <label class="font-weight-bold">Release Description</label>
                        <div class="form-control-plaintext bg-light rounded p-3">
                            {{ $row->release_description ?: 'No description provided.' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="font-weight-bold">Priority</label>
                            <div>
                                <span class="label label-light-primary label-lg font-weight-bold">
                                    {{ $row->priority->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold">Release Start Date</label>
                            <div class="font-weight-bolder">
                                {{ $row->release_start_date ? \Carbon\Carbon::parse($row->release_start_date)->format('d M Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold">Go Live Planned Date</label>
                            <div class="font-weight-bolder">
                                {{ $row->go_live_planned_date ? \Carbon\Carbon::parse($row->go_live_planned_date)->format('d M Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold">Responsible RTM</label>
                            <div class="font-weight-bolder">{{ $row->responsibleRtm->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: TESTING SCHEDULE --}}
            @php
                $hasTestDates = $row->atp_review_start_date || $row->vendor_internal_test_start_date ||
                                $row->iot_start_date || $row->e2e_start_date ||
                                $row->uat_start_date || $row->smoke_test_start_date;
            @endphp
            @if($hasTestDates)
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-warning py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-tasks text-warning mr-2"></i>Testing Schedule
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $testPhases = [
                            ['label' => 'ATP Review', 'icon' => 'la-file-alt', 'color' => 'primary', 'start' => $row->atp_review_start_date, 'end' => $row->atp_review_end_date],
                            ['label' => 'Vendor Internal Test', 'icon' => 'la-flask', 'color' => 'info', 'start' => $row->vendor_internal_test_start_date, 'end' => $row->vendor_internal_test_end_date],
                            ['label' => 'IOT', 'icon' => 'la-cogs', 'color' => 'success', 'start' => $row->iot_start_date, 'end' => $row->iot_end_date],
                            ['label' => 'E2E Testing', 'icon' => 'la-project-diagram', 'color' => 'warning', 'start' => $row->e2e_start_date, 'end' => $row->e2e_end_date],
                            ['label' => 'UAT', 'icon' => 'la-user-check', 'color' => 'primary', 'start' => $row->uat_start_date, 'end' => $row->uat_end_date],
                            ['label' => 'Smoke Test', 'icon' => 'la-fire', 'color' => 'danger', 'start' => $row->smoke_test_start_date, 'end' => $row->smoke_test_end_date],
                        ];
                    @endphp
                    <div class="row">
                        @foreach($testPhases as $phase)
                            @if($phase['start'] || $phase['end'])
                            <div class="col-md-4 mb-4">
                                <div class="bg-light rounded p-4 h-100">
                                    <h6 class="font-weight-bolder text-dark mb-3">
                                        <i class="la {{ $phase['icon'] }} text-{{ $phase['color'] }} mr-2"></i>{{ $phase['label'] }}
                                    </h6>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="text-muted font-size-xs">Start</span>
                                            <div class="font-weight-bolder font-size-sm">
                                                {{ $phase['start'] ? \Carbon\Carbon::parse($phase['start'])->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-muted font-size-xs">End</span>
                                            <div class="font-weight-bolder font-size-sm">
                                                {{ $phase['end'] ? \Carbon\Carbon::parse($phase['end'])->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- SECTION 4: RELATED CRs --}}
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-primary py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-link text-primary mr-2"></i>Related Change Requests
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-light-primary label-lg font-weight-bold">{{ $changeRequests->count() }} CRs</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($changeRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center mb-0">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="pl-6">CR #</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($changeRequests as $cr)
                                <tr>
                                    <td class="pl-6">
                                        <span class="font-weight-bolder text-primary">#{{ $cr->cr_no ?? $cr->id }}</span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-dark-75">{{ \Illuminate\Support\Str::limit($cr->title ?? $cr->cr_summary ?? '-', 60) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $crStatus = $cr->activeStatus->status ?? null;
                                        @endphp
                                        @if($crStatus)
                                            <span class="label label-light-info label-inline font-weight-bold">{{ $crStatus->status_name ?? '-' }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-dark-75">{{ $cr->priority->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('change_request.edit', $cr->id) }}" class="btn btn-icon btn-light-primary btn-sm" title="View CR">
                                            <i class="la la-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="la la-link text-muted" style="font-size: 50px; opacity: 0.3;"></i>
                        <p class="text-muted font-weight-bold mt-3">No change requests linked to this release.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SECTION 5: TEAM MEMBERS --}}
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-success py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-user-friends text-success mr-2"></i>Release Team
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-light-success label-lg font-weight-bold">{{ $teamMembers->count() }} Members</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($teamMembers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center mb-0">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="pl-6">Member</th>
                                    <th>Role</th>
                                    <th>Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teamMembers as $member)
                                <tr>
                                    <td class="pl-6">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35 symbol-circle symbol-light-primary mr-3">
                                                <span class="symbol-label font-weight-bolder">
                                                    {{ strtoupper(substr($member->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="font-weight-bolder text-dark-75">{{ $member->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="label label-light-info label-inline font-weight-bold">
                                            {{ $member->role->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-muted font-size-sm">
                                        {{ $member->created_at ? $member->created_at->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="la la-user-friends text-muted" style="font-size: 50px; opacity: 0.3;"></i>
                        <p class="text-muted font-weight-bold mt-3">No team members assigned yet.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SECTION 6: STAKEHOLDER REGISTER --}}
            @if($stakeholders->count() > 0)
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-info py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-user text-info mr-2"></i>Stakeholder Register
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-light-info label-lg font-weight-bold">{{ $stakeholders->count() }} Records</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center mb-0">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="pl-6">CR #</th>
                                    <th>High Impact</th>
                                    <th>Moderate Impact</th>
                                    <th>Low Impact</th>
                                    <th>Added By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stakeholders as $sh)
                                <tr>
                                    <td class="pl-6">
                                        <span class="font-weight-bolder text-primary">#{{ $sh->changeRequest->cr_no ?? $sh->cr_id }}</span>
                                    </td>
                                    <td>{{ $sh->high_impact_stakeholder ?? '-' }}</td>
                                    <td>{{ $sh->moderate_impact_stakeholder ?? '-' }}</td>
                                    <td>{{ $sh->low_impact_stakeholder ?? '-' }}</td>
                                    <td class="text-muted font-size-sm">{{ $sh->creator->name ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- SECTION 7: RISK REGISTER --}}
            @if($risks->count() > 0)
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-danger py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-exclamation-triangle text-danger mr-2"></i>Risk Register
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-light-danger label-lg font-weight-bold">{{ $risks->count() }} Risks</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center mb-0">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="pl-6">Risk ID</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th class="text-center">Impact</th>
                                    <th class="text-center">Likelihood</th>
                                    <th class="text-center">Score</th>
                                    <th>Status</th>
                                    <th>Owner</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($risks as $risk)
                                <tr>
                                    <td class="pl-6">
                                        <span class="font-weight-bolder text-primary">{{ $risk->risk_id }}</span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-dark-75">{{ \Illuminate\Support\Str::limit($risk->risk_description, 50) }}</span>
                                        @if($risk->changeRequest)
                                            <br><span class="text-muted font-size-xs">CR #{{ $risk->changeRequest->cr_no ?? $risk->cr_id }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="label label-light-info label-inline font-weight-bold">{{ $risk->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center font-weight-bolder">{{ $risk->impact_level }}</td>
                                    <td class="text-center font-weight-bolder">{{ $risk->probability }}</td>
                                    <td class="text-center">
                                        <span class="label label-{{ $risk->risk_score_color }} label-pill label-inline font-weight-bolder" style="min-width: 40px;">
                                            {{ $risk->risk_score }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">{{ $risk->status->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-muted">{{ $risk->owner ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- SECTION 8: TECHNICAL ATTACHMENTS --}}
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-success py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-paperclip text-success mr-2"></i>Technical Attachments
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-light-success label-lg font-weight-bold">{{ $attachments->count() }} Files</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($attachments->count() > 0)
                        @foreach($attachments as $attachment)
                            <div class="d-flex align-items-center bg-light rounded p-3 mb-2">
                                <div class="symbol symbol-35 symbol-light-primary mr-3 flex-shrink-0">
                                    <span class="symbol-label"><i class="la la-file-alt text-primary icon-lg"></i></span>
                                </div>
                                <div class="flex-grow-1">
                                    <a href="{{ route('releases.attachments.download', $attachment->id) }}" class="text-dark font-weight-bolder text-hover-primary font-size-sm">
                                        {{ $attachment->file_name }}
                                    </a>
                                    <span class="text-muted font-size-xs d-block">
                                        Uploaded {{ $attachment->created_at ? $attachment->created_at->format('d M Y, H:i') : '' }}
                                    </span>
                                </div>
                                <a href="{{ route('releases.attachments.download', $attachment->id) }}" class="btn btn-icon btn-light-primary btn-sm" title="Download">
                                    <i class="la la-download"></i>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-6">
                            <i class="la la-paperclip text-muted" style="font-size: 40px; opacity: 0.3;"></i>
                            <p class="text-muted font-weight-bold mt-2 mb-0">No technical attachments uploaded.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SECTION 9: CR ATTACHMENTS --}}
            @if($crAttachments->count() > 0)
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-warning py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-file-archive text-warning mr-2"></i>CR Attachments
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-light-warning label-lg font-weight-bold">{{ $crAttachments->count() }} Files</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center mb-0">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="pl-6">CR #</th>
                                    <th>File</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($crAttachments as $att)
                                <tr>
                                    <td class="pl-6">
                                        <span class="font-weight-bolder text-primary">#{{ optional($att->changeRequest)->cr_no ?? $att->cr_id }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('releases.cr-attachments.download', $att->id) }}" class="text-hover-primary font-weight-bold">
                                            <i class="la la-file-download mr-1 text-info"></i>{{ $att->file_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($att->type)
                                            <span class="label label-light-info label-inline font-weight-bold">{{ $att->type }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $att->description ?? '-' }}</td>
                                    <td class="text-muted">{{ $att->creator->name ?? '-' }}</td>
                                    <td class="text-muted font-size-sm">{{ $att->created_at ? $att->created_at->format('d M Y, H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- SECTION 10: TECHNICAL FEEDBACK --}}
            @if($feedbacks->count() > 0)
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header bg-light-info py-4">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-comment-dots text-info mr-2"></i>Technical Feedback
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-light-info label-lg font-weight-bold">{{ $feedbacks->count() }} Entries</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-3">
                        @foreach($feedbacks->sortByDesc('created_at') as $feedback)
                        <div class="timeline-item mb-3">
                            <div class="timeline-media bg-light-primary">
                                <i class="la la-comment text-primary"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="bg-light rounded p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-weight-bolder text-dark">{{ $feedback->creator->name ?? 'Unknown' }}</span>
                                        <span class="text-muted font-size-sm">{{ $feedback->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="mb-0">{{ $feedback->feedback }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- SECTION 11: ACTIVITY LOGS --}}
            <div class="card card-custom card-border shadow-sm gutter-b">
                <div class="card-header py-4" style="background: #f8f5ff;">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark">
                            <i class="la la-history mr-2" style="color: #8c08aa;"></i>Activity Logs
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="label label-lg font-weight-bold" style="background: #f0e6f6; color: #8c08aa;">
                            {{ $releaseLogs->count() }} Entries
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($releaseLogs->count() > 0)
                    <div class="timeline timeline-3">
                        @foreach($releaseLogs->take(50) as $log)
                        <div class="timeline-item mb-3">
                            <div class="timeline-media" style="background: #f0e6f6;">
                                <i class="la la-clipboard-list" style="color: #8c08aa;"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="font-weight-bold text-dark-75">{{ $log->log_text }}</span>
                                        <span class="text-muted font-size-xs d-block mt-1">
                                            by {{ $log->user->name ?? 'System' }}
                                        </span>
                                    </div>
                                    <span class="text-muted font-size-sm flex-shrink-0 ml-3">
                                        {{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="la la-history text-muted" style="font-size: 50px; opacity: 0.3;"></i>
                        <p class="text-muted font-weight-bold mt-3">No activity logs recorded yet.</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

<style>
    .release-progress-stepper { position: relative; padding: 10px 20px 0; }
    .stepper-track {
        position: absolute; top: 23px; left: 40px; right: 40px; height: 3px;
        background: linear-gradient(90deg, #E4E6EF 0%, #E4E6EF 100%);
        border-radius: 2px;
    }
    .stepper-dot-active {
        animation: pulse-ring 2s ease-in-out infinite;
    }
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 4px rgba(140, 8, 170, 0.41); }
        50% { box-shadow: 0 0 0 8px rgba(140, 8, 170, 0.08); }
        100% { box-shadow: 0 0 0 4px rgba(140, 8, 170, 0.41); }
    }
</style>