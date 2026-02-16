@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <h2 class="text-white font-weight-bold my-2 mr-3">Release Management</h2>
                        <span class="label label-light-primary label-inline font-weight-bold">#{{ $row->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            {{-- Status Progress Bar --}}
            @include('releases.partials.status_bar', ['release' => $row])

            {{-- Main Card with Tabs --}}
            <div class="card card-custom gutter-b shadow-sm">
                <div class="card-header card-header-tabs-line" style="border-bottom: 2px solid #EBEDF3; min-height: 55px;">
                    <div class="card-toolbar">
                        <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x" role="tablist" id="releaseTabs" style="border-bottom: none;">
                            <li class="nav-item mr-1">
                                <a class="nav-link active py-3 px-5" data-toggle="tab" href="#tab_release" role="tab">
                                    <span class="nav-icon"><i class="la la-file-alt icon-lg"></i></span>
                                    <span class="nav-text font-weight-bolder font-size-sm">Release</span>
                                </a>
                            </li>
                            <li class="nav-item mr-1">
                                <a class="nav-link py-3 px-5" data-toggle="tab" href="#tab_release_plan" role="tab">
                                    <span class="nav-icon"><i class="la la-calendar icon-lg"></i></span>
                                    <span class="nav-text font-weight-bolder font-size-sm">Release Plan</span>
                                </a>
                            </li>
                            <li class="nav-item mr-1">
                                <a class="nav-link py-3 px-5" data-toggle="tab" href="#tab_stakeholder" role="tab">
                                    <span class="nav-icon"><i class="la la-users icon-lg"></i></span>
                                    <span class="nav-text font-weight-bolder font-size-sm">Stakeholders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 px-5" data-toggle="tab" href="#tab_risk" role="tab">
                                    <span class="nav-icon"><i class="la la-exclamation-triangle icon-lg"></i></span>
                                    <span class="nav-text font-weight-bolder font-size-sm">Risk Register</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" id="openLogsModal" class="btn btn-light-primary font-weight-bolder btn-sm py-2 px-4" style="border-radius: 6px;">
                            <i class="la la-history icon-md"></i> <span class="d-none d-sm-inline">View Logs</span>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- Tab 1: Release --}}
                        <div class="tab-pane fade show active" id="tab_release" role="tabpanel">
                            {{-- Form for Sections 1-5 (Release Info, Planning, Contact, Technical, Status) --}}
                            <form action="{{ route('releases.update', $row->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                @include('releases.partials.tabs.release', [
                                    'row' => $row, 
                                    'vendors' => $vendors, 
                                    'priorities' => $priorities,
                                    'users' => $users ?? collect()
                                ])
                                <div class="d-flex justify-content-end mt-5 mb-5">
                                    <a href="{{ route('releases.index') }}" class="btn btn-secondary font-weight-bolder px-8 mr-3">
                                        <i class="la la-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="la la-save"></i> Save Release
                                    </button>
                                </div>
                            </form>

                            {{-- ============================================================ --}}
                            {{-- AJAX Sections (Outside Form) --}}
                            {{-- ============================================================ --}}

                            @php
                                $linkedCrs = $row->changeRequests ?? collect();
                            @endphp

                            {{-- Section 6: Related Change Requests --}}
                            <div class="card card-custom card-border shadow-sm mt-6">
                                <div class="card-header bg-light-success py-4">
                                    <div class="card-title">
                                        <h3 class="card-label font-weight-bolder text-dark">
                                            <i class="la la-link text-success mr-2"></i>Related Change Requests
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <span class="label label-light-primary label-lg font-weight-bold" id="release_cr_count">
                                            {{ $linkedCrs->count() }} CRs
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{-- Search & Link Input --}}
                                    <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
                                        <div class="alert-icon"><i class="flaticon2-search-1"></i></div>
                                        <div class="alert-text">
                                            <div class="input-group">
                                                <input type="text" id="release_cr_no" class="form-control" placeholder="Enter Vendor CR number to link..." 
                                                       onkeypress="if(event.keyCode==13){document.getElementById('release_cr_search_btn').click();return false;}">
                                                <div class="input-group-append">
                                                    <button type="button" id="release_cr_search_btn" class="btn btn-primary font-weight-bold">
                                                        <i class="la la-search mr-1"></i>Search & Link
                                                    </button>
                                                </div>
                                            </div>
                                            <small id="release_cr_search_message" class="form-text text-danger mt-2 font-weight-bold"></small>
                                            <small class="form-text text-muted mt-1">Only Vendor CRs (workflow type = Vendor) can be linked to releases.</small>
                                        </div>
                                    </div>

                                    {{-- Search Result Preview --}}
                                    <div id="release_cr_search_result" class="card card-custom bg-light-success gutter-b" style="display:none;">
                                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                                            <div>
                                                <span class="font-weight-bolder mr-2">Found:</span>
                                                <span id="release_cr_result_no" class="font-weight-bold mr-3"></span>
                                                <span id="release_cr_result_title" class="mr-3"></span>
                                                <span class="label label-inline label-white mr-3" id="release_cr_result_status"></span>
                                            </div>
                                            <div>
                                                <a href="#" target="_blank" id="release_cr_result_link" class="btn btn-sm btn-light-primary font-weight-bold mr-2">
                                                    <i class="la la-external-link-alt"></i> View CR
                                                </a>
                                                <button type="button" id="release_cr_attach_btn" class="btn btn-sm btn-success font-weight-bold">
                                                    <i class="la la-plus"></i> Link to Release
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CRs Table --}}
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-hover" id="release_cr_table">
                                            <thead>
                                                <tr class="text-left text-uppercase font-size-sm">
                                                    <th style="width: 80px">CR ID #</th>
                                                    <th style="min-width: 150px">Subject</th>
                                                    <th>CR Status</th>
                                                    <th class="text-center">Need IOT</th>
                                                    <th class="text-center">Need E2E</th>
                                                    <th class="text-center">Need UAT</th>
                                                    <th>Requester</th>
                                                    <th>MDs</th>
                                                    <th class="text-center">Need SR/CRD</th>
                                                    <th class="text-center">Top Mgmt</th>
                                                    <th>KPI Name</th>
                                                    <th>Tech/Business</th>
                                                    <th class="text-center" style="width: 60px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="release_cr_table_body">
                                                @forelse($linkedCrs as $cr)
                                                    <tr data-cr-id="{{ $cr->id }}">
                                                        <td class="font-weight-bolder text-primary">
                                                            <a href="{{ route('show.cr', $cr->id) }}" target="_blank">#{{ $cr->cr_no }}</a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('show.cr', $cr->id) }}" target="_blank" class="text-dark-75 text-hover-primary font-weight-bold">
                                                                {{ Str::limit($cr->title, 40) }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="label label-lg label-light-info label-inline font-weight-bold">
                                                                {{ optional(optional($cr->CurrentRequestStatuses)->status)->status_name ?? '-' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            @php $needIot = $cr->need_iot ?? null; @endphp
                                                            @if($needIot === null || $needIot === '')
                                                                <span class="text-muted">NA</span>
                                                            @else
                                                                <span class="label label-{{ $needIot ? 'success' : 'secondary' }} label-dot mr-2"></span>
                                                                {{ $needIot ? 'Yes' : 'No' }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @php $needE2e = $cr->need_e2e ?? null; @endphp
                                                            @if($needE2e === null || $needE2e === '')
                                                                <span class="text-muted">NA</span>
                                                            @else
                                                                <span class="label label-{{ $needE2e ? 'success' : 'secondary' }} label-dot mr-2"></span>
                                                                {{ $needE2e ? 'Yes' : 'No' }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @php $needUat = $cr->need_uat ?? null; @endphp
                                                            @if($needUat === null || $needUat === '')
                                                                <span class="text-muted">NA</span>
                                                            @else
                                                                <span class="label label-{{ $needUat ? 'success' : 'secondary' }} label-dot mr-2"></span>
                                                                {{ $needUat ? 'Yes' : 'No' }}
                                                            @endif
                                                        </td>
                                                        <td>{{ optional($cr->requester)->name ?? '-' }}</td>
                                                        <td>{{ $cr->man_days ?? 'NA' }}</td>
                                                        <td class="text-center">
                                                            @php $needSrCrd = $cr->need_sr_crd ?? null; @endphp
                                                            @if($needSrCrd === null || $needSrCrd === '')
                                                                <span class="text-muted">NA</span>
                                                            @else
                                                                <span class="label label-{{ $needSrCrd ? 'success' : 'secondary' }} label-dot mr-2"></span>
                                                                {{ $needSrCrd ? 'Yes' : 'No' }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @php $topMgmt = $cr->top_management ?? null; @endphp
                                                            @if($topMgmt === null || $topMgmt === '')
                                                                <span class="text-muted">NA</span>
                                                            @else
                                                                <span class="label label-{{ $topMgmt ? 'warning' : 'secondary' }} label-inline">
                                                                    {{ $topMgmt ? 'Yes' : 'No' }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($cr->kpis && $cr->kpis->isNotEmpty())
                                                                {{ $cr->kpis->pluck('name')->implode(', ') }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $cr->technical_business ?? 'NA' }}</td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-icon btn-light-danger btn-sm js-release-detach-cr" 
                                                                    data-cr-id="{{ $cr->id }}" data-cr-no="{{ $cr->cr_no }}" title="Remove CR from Release">
                                                                <i class="la la-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="release-no-records">
                                                        <td colspan="13" class="text-center text-muted font-weight-bold py-8">
                                                            <i class="la la-inbox text-muted font-size-h1 d-block mb-3"></i>
                                                            No Change Requests linked to this Release.<br>
                                                            <small>Use the search box above to link Vendor CRs.</small>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 7: Release Team --}}
                            <div class="card card-custom card-border shadow-sm mt-6">
                                <div class="card-header bg-light-warning py-4">
                                    <div class="card-title">
                                        <h3 class="card-label font-weight-bolder text-dark">
                                            <i class="la la-user-friends text-warning mr-2"></i>Release Team
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <span class="label label-light-warning label-lg font-weight-bold" id="release_team_count">
                                            {{ ($row->teamMembers ?? collect())->count() }} Members
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{-- Add Team Member Form --}}
                                    <div class="bg-light rounded p-5 mb-5">
                                        <h6 class="font-weight-bolder text-dark mb-4">
                                            <i class="la la-plus-circle text-primary mr-1"></i>Add Team Member
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group mb-md-0">
                                                    <label class="font-size-sm font-weight-bold">Role <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="team_role_id">
                                                        <option value="">Select Role...</option>
                                                        @foreach($releaseTeamRoles as $role)
                                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-md-0">
                                                    <label class="font-size-sm font-weight-bold">User <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="team_user_id">
                                                        <option value="">Select User...</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-md-0">
                                                    <label class="font-size-sm font-weight-bold">Mobile <span class="text-danger">*</span> <small class="text-muted">(11 digits)</small></label>
                                                    <input type="text" class="form-control" id="team_mobile" placeholder="01XXXXXXXXX" maxlength="11">
                                                </div>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end">
                                                <button type="button" id="add_team_member_btn" class="btn btn-success font-weight-bold w-100">
                                                    <i class="la la-plus mr-1"></i>Add Member
                                                </button>
                                            </div>
                                        </div>
                                        <small id="team_member_message" class="form-text mt-2 font-weight-bold"></small>
                                    </div>

                                    {{-- Team Members Table --}}
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-hover">
                                            <thead>
                                                <tr class="text-left text-uppercase font-size-sm">
                                                    <th>Role</th>
                                                    <th>User Name</th>
                                                    <th>Mobile</th>
                                                    <th>Added By</th>
                                                    <th>Date</th>
                                                    <th class="text-center" style="width: 60px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="team_members_table_body">
                                                @forelse($row->teamMembers ?? collect() as $member)
                                                    <tr data-member-id="{{ $member->id }}">
                                                        <td>
                                                            <span class="label label-lg label-light-warning label-inline font-weight-bold">
                                                                {{ $member->role->name ?? '-' }}
                                                            </span>
                                                        </td>
                                                        <td class="font-weight-bold">{{ $member->user->name ?? '-' }}</td>
                                                        <td>{{ $member->mobile }}</td>
                                                        <td class="text-muted">{{ $member->creator->name ?? '-' }}</td>
                                                        <td class="text-muted font-size-sm">{{ $member->created_at ? $member->created_at->format('d M Y, H:i') : '-' }}</td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-icon btn-light-danger btn-sm js-remove-team-member" 
                                                                    data-member-id="{{ $member->id }}" data-member-name="{{ $member->user->name ?? '' }}" title="Remove Member">
                                                                <i class="la la-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="team-no-records">
                                                        <td colspan="6" class="text-center text-muted font-weight-bold py-8">
                                                            <i class="la la-users text-muted font-size-h1 d-block mb-3"></i>
                                                            No team members added yet.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 8: CR Attachments --}}
                            <div class="card card-custom card-border shadow-sm mt-6">
                                <div class="card-header bg-light-info py-4">
                                    <div class="card-title">
                                        <h3 class="card-label font-weight-bolder text-dark">
                                            <i class="la la-paperclip text-info mr-2"></i>CR Attachments
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <span class="label label-light-info label-lg font-weight-bold" id="cr_attachments_count">
                                            {{ ($row->crAttachments ?? collect())->count() }} Files
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{-- Upload Attachment Form --}}
                                    <div class="bg-light rounded p-5 mb-5">
                                        <h6 class="font-weight-bolder text-dark mb-4">
                                            <i class="la la-cloud-upload-alt text-info mr-1"></i>Upload Attachment
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group mb-md-0">
                                                    <label class="font-size-sm font-weight-bold">Related CR <span class="text-danger">*</span></label>
                                                    <select class="form-control select2" id="cr_attachment_cr_id">
                                                        <option value="">Select CR...</option>
                                                        @foreach($linkedCrs as $cr)
                                                            <option value="{{ $cr->id }}">CR #{{ $cr->cr_no }} - {{ Str::limit($cr->title, 30) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-md-0">
                                                    <label class="font-size-sm font-weight-bold">Type</label>
                                                    <input type="text" class="form-control" id="cr_attachment_type" placeholder="e.g. Design, Test">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-md-0">
                                                    <label class="font-size-sm font-weight-bold">Description</label>
                                                    <input type="text" class="form-control" id="cr_attachment_description" placeholder="Brief description...">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-md-0">
                                                    <label class="font-size-sm font-weight-bold">File <span class="text-danger">*</span></label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="cr_attachment_file">
                                                        <label class="custom-file-label font-size-sm" for="cr_attachment_file">Choose file...</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" id="upload_cr_attachment_btn" class="btn btn-info font-weight-bold w-100">
                                                    <i class="la la-upload mr-1"></i>Upload
                                                </button>
                                            </div>
                                        </div>
                                        <small id="cr_attachment_message" class="form-text mt-2 font-weight-bold"></small>
                                    </div>

                                    {{-- Attachments Table --}}
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-hover">
                                            <thead>
                                                <tr class="text-left text-uppercase font-size-sm">
                                                    <th>CR #</th>
                                                    <th>File</th>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                    <th>Updated By</th>
                                                    <th>Date/Time</th>
                                                    <th class="text-center" style="width: 60px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cr_attachments_table_body">
                                                @forelse($row->crAttachments ?? collect() as $att)
                                                    <tr data-attachment-id="{{ $att->id }}">
                                                        <td class="font-weight-bolder text-primary">
                                                            #{{ optional($att->changeRequest)->cr_no ?? $att->cr_id }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('releases.cr-attachments.download', $att->id) }}" target="_blank" class="text-hover-primary font-weight-bold">
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
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-icon btn-light-danger btn-sm js-remove-cr-attachment" 
                                                                    data-attachment-id="{{ $att->id }}" data-file-name="{{ $att->file_name }}" title="Delete Attachment">
                                                                <i class="la la-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="attachment-no-records">
                                                        <td colspan="7" class="text-center text-muted font-weight-bold py-8">
                                                            <i class="la la-paperclip text-muted font-size-h1 d-block mb-3"></i>
                                                            No attachments uploaded yet.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Release Plan --}}
                        <div class="tab-pane fade" id="tab_release_plan" role="tabpanel">
                            <form action="{{ route('releases.update', $row->id) }}?tab=tab_release_plan" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                @include('releases.partials.tabs.release_plan', ['row' => $row])
                                <div class="text-right mt-5">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="la la-save"></i> Save Release Plan
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Tab 3: Stakeholder Register --}}
                        <div class="tab-pane fade" id="tab_stakeholder" role="tabpanel">
                            @include('releases.partials.tabs.stakeholder_register', [
                                'row' => $row,
                                'users' => $users ?? collect()
                            ])
                        </div>

                        {{-- Tab 4: Release Risk Register --}}
                        <div class="tab-pane fade" id="tab_risk" role="tabpanel">
                            @include('releases.partials.tabs.release_risk_register', [
                                'row' => $row,
                                'riskCategories' => $riskCategories ?? collect(),
                                'riskStatuses' => $riskStatuses ?? collect(),
                                'users' => $users ?? collect()
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Logs Modal (KPI-style) --}}
<div class="modal fade" id="releaseLogsModal" tabindex="-1" role="dialog" aria-labelledby="releaseLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="releaseLogsModalLabel">
                    <i class="flaticon-list-3 mr-2 text-white"></i> Release History Logs
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light" id="releaseLogsContent" style="max-height: 70vh; overflow-y: auto;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Loading logs...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        var releaseId = {{ $row->id }};
        var csrfToken = '{{ csrf_token() }}';

        // Initialize Select2 for all dropdowns
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });

        // Get active tab from controller (query string ?tab=)
        var activeTab = '{{ $activeTab ?? "tab_release" }}';
        
        // Activate the correct tab
        if (activeTab && activeTab !== 'tab_release') {
            $('#releaseTabs a[href="#' + activeTab + '"]').tab('show');
        }

        // Update URL query string when switching tabs
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tabId = $(e.target).attr('href').replace('#', '');
            var url = new URL(window.location.href);
            url.searchParams.set('tab', tabId);
            url.hash = ''; // Remove any hash
            history.replaceState(null, null, url.toString());
        });

        // ========================================
        // Logs Modal
        // ========================================
        $('#openLogsModal').on('click', function() {
            $('#releaseLogsModal').modal('show');
            
            // Load logs via AJAX
            $.ajax({
                url: '{{ url("release/logs") }}/' + releaseId,
                method: 'GET',
                success: function(response) {
                    $('#releaseLogsContent').html(response);
                },
                error: function() {
                    $('#releaseLogsContent').html(
                        '<div class="text-center py-5 text-danger">' +
                        '<i class="la la-exclamation-triangle font-size-h1 d-block mb-3"></i>' +
                        'Failed to load logs. Please try again.' +
                        '</div>'
                    );
                }
            });
        });

        // ========================================
        // Related Change Requests AJAX Handlers
        // ========================================
        var foundCrData = null;

        // Search CR
        $('#release_cr_search_btn').on('click', function() {
            var crNo = $('#release_cr_no').val().trim();
            var $searchBtn = $(this);
            var $message = $('#release_cr_search_message');
            var $result = $('#release_cr_search_result');

            $message.text('').removeClass('text-danger text-success');
            $result.hide();
            foundCrData = null;

            if (!crNo || isNaN(crNo)) {
                $message.text('Please enter a valid CR number.').addClass('text-danger');
                return;
            }

            $searchBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm mr-1"></i>Searching...');

            $.ajax({
                url: "{{ route('releases.search-cr', ':id') }}".replace(':id', releaseId),
                method: 'GET',
                data: { cr_no: crNo },
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    if (response.success && response.data) {
                        foundCrData = response.data;
                        $('#release_cr_result_no').text('CR #' + response.data.cr_no);
                        $('#release_cr_result_title').text(response.data.title);
                        $('#release_cr_result_status').text(response.data.status);
                        $('#release_cr_result_link').attr('href', '/show_cr/' + response.data.id);
                        $result.slideDown();
                        $message.text('CR found! Click "Link to Release" to attach it.').removeClass('text-danger').addClass('text-success');
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.message || 'An error occurred while searching.';
                    $message.text(errorMsg).addClass('text-danger');
                },
                complete: function() {
                    $searchBtn.prop('disabled', false).html('<i class="la la-search mr-1"></i>Search & Link');
                }
            });
        });

        // Attach CR
        $('#release_cr_attach_btn').on('click', function() {
            if (!foundCrData) return;
            var $attachBtn = $(this);
            var $message = $('#release_cr_search_message');
            $attachBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm mr-1"></i>Linking...');

            $.ajax({
                url: "{{ route('releases.attach-cr', ':id') }}".replace(':id', releaseId),
                method: 'POST',
                data: { cr_no: foundCrData.cr_no },
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, 'Success');
                        $('#release_cr_no').val('');
                        $('#release_cr_search_result').slideUp();
                        foundCrData = null;
                        setTimeout(function() { location.reload(); }, 1000);
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.message || 'An error occurred while linking.';
                    $message.text(errorMsg).addClass('text-danger').removeClass('text-success');
                    toastr.error(errorMsg, 'Error');
                },
                complete: function() {
                    $attachBtn.prop('disabled', false).html('<i class="la la-plus"></i> Link to Release');
                }
            });
        });

        // Detach CR
        $(document).on('click', '.js-release-detach-cr', function() {
            var $btn = $(this);
            var crId = $btn.data('cr-id');
            var crNo = $btn.data('cr-no');

            Swal.fire({
                title: 'Remove CR #' + crNo + '?',
                text: 'This will unlink the CR from this release.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F64E60',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="la la-trash"></i> Yes, Remove',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i>');
                    $.ajax({
                        url: "{{ route('releases.detach-cr', ':id') }}".replace(':id', releaseId),
                        method: 'DELETE',
                        data: { cr_id: crId },
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message, 'Success');
                                $btn.closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    var count = $('#release_cr_table_body tr:not(.release-no-records)').length;
                                    $('#release_cr_count').text(count + ' CRs');
                                    if (count === 0) {
                                        $('#release_cr_table_body').html(
                                            '<tr class="release-no-records"><td colspan="13" class="text-center text-muted font-weight-bold py-8">' +
                                            '<i class="la la-inbox text-muted font-size-h1 d-block mb-3"></i>' +
                                            'No Change Requests linked to this Release.<br><small>Use the search box above to link Vendor CRs.</small></td></tr>'
                                        );
                                    }
                                });
                                setTimeout(function() { location.reload(); }, 1500);
                            }
                        },
                        error: function(xhr) {
                            var errorMsg = xhr.responseJSON?.message || 'An error occurred while removing.';
                            toastr.error(errorMsg, 'Error');
                            $btn.prop('disabled', false).html('<i class="la la-trash"></i>');
                        }
                    });
                }
            });
        });

        // ========================================
        // Release Team Members AJAX Handlers
        // ========================================
        $('#add_team_member_btn').on('click', function() {
            var roleId = $('#team_role_id').val();
            var userId = $('#team_user_id').val();
            var mobile = $('#team_mobile').val().trim();
            var $btn = $(this);
            var $message = $('#team_member_message');

            $message.text('').removeClass('text-danger text-success');

            // Client-side validation
            if (!roleId) {
                $message.text('Please select a role.').addClass('text-danger');
                return;
            }
            if (!userId) {
                $message.text('Please select a user.').addClass('text-danger');
                return;
            }
            if (!mobile || mobile.length !== 11 || !/^\d{11}$/.test(mobile)) {
                $message.text('Mobile number must be exactly 11 digits.').addClass('text-danger');
                return;
            }

            $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm mr-1"></i>Adding...');

            $.ajax({
                url: "{{ route('releases.team-members.store', ':id') }}".replace(':id', releaseId),
                method: 'POST',
                data: { role_id: roleId, user_id: userId, mobile: mobile },
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, 'Success');
                        var m = response.member;

                        // Remove empty row
                        $('.team-no-records').remove();

                        // Add new row
                        var newRow = '<tr data-member-id="' + m.id + '">' +
                            '<td><span class="label label-lg label-light-warning label-inline font-weight-bold">' + m.role_name + '</span></td>' +
                            '<td class="font-weight-bold">' + m.user_name + '</td>' +
                            '<td>' + m.mobile + '</td>' +
                            '<td class="text-muted">' + m.created_by + '</td>' +
                            '<td class="text-muted font-size-sm">' + m.created_at + '</td>' +
                            '<td class="text-center">' +
                                '<button type="button" class="btn btn-icon btn-light-danger btn-sm js-remove-team-member" ' +
                                'data-member-id="' + m.id + '" data-member-name="' + m.user_name + '" title="Remove Member">' +
                                '<i class="la la-trash"></i></button>' +
                            '</td></tr>';
                        $('#team_members_table_body').append(newRow);

                        // Update count
                        var count = $('#team_members_table_body tr:not(.team-no-records)').length;
                        $('#release_team_count').text(count + ' Members');

                        // Clear form
                        $('#team_role_id').val('').trigger('change');
                        $('#team_user_id').val('').trigger('change');
                        $('#team_mobile').val('');
                        $message.text('Team member added successfully.').addClass('text-success');
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.message || 'Failed to add team member.';
                    if (xhr.responseJSON?.errors) {
                        var errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errors.join(' ');
                    }
                    $message.text(errorMsg).addClass('text-danger');
                    toastr.error(errorMsg, 'Error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="la la-plus mr-1"></i>Add Member');
                }
            });
        });

        // Remove Team Member
        $(document).on('click', '.js-remove-team-member', function() {
            var $btn = $(this);
            var memberId = $btn.data('member-id');
            var memberName = $btn.data('member-name');

            Swal.fire({
                title: 'Remove ' + memberName + '?',
                text: 'This will remove the team member from this release.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F64E60',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="la la-trash"></i> Yes, Remove',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i>');
                    $.ajax({
                        url: "{{ route('releases.team-members.destroy', [':release', ':member']) }}".replace(':release', releaseId).replace(':member', memberId),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message, 'Success');
                                $btn.closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    var count = $('#team_members_table_body tr:not(.team-no-records)').length;
                                    $('#release_team_count').text(count + ' Members');
                                    if (count === 0) {
                                        $('#team_members_table_body').html(
                                            '<tr class="team-no-records"><td colspan="6" class="text-center text-muted font-weight-bold py-8">' +
                                            '<i class="la la-users text-muted font-size-h1 d-block mb-3"></i>No team members added yet.</td></tr>'
                                        );
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            var errorMsg = xhr.responseJSON?.message || 'Failed to remove team member.';
                            toastr.error(errorMsg, 'Error');
                            $btn.prop('disabled', false).html('<i class="la la-trash"></i>');
                        }
                    });
                }
            });
        });

        // ========================================
        // CR Attachments AJAX Handlers
        // ========================================

        // File input label update
        $('#cr_attachment_file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choose file...');
        });

        // Upload Attachment
        $('#upload_cr_attachment_btn').on('click', function() {
            var crId = $('#cr_attachment_cr_id').val();
            var type = $('#cr_attachment_type').val().trim();
            var description = $('#cr_attachment_description').val().trim();
            var fileInput = document.getElementById('cr_attachment_file');
            var $btn = $(this);
            var $message = $('#cr_attachment_message');

            $message.text('').removeClass('text-danger text-success');

            if (!crId) {
                $message.text('Please select a CR.').addClass('text-danger');
                return;
            }
            if (!fileInput.files || fileInput.files.length === 0) {
                $message.text('Please select a file to upload.').addClass('text-danger');
                return;
            }

            var formData = new FormData();
            formData.append('cr_id', crId);
            formData.append('type', type);
            formData.append('description', description);
            formData.append('attachment', fileInput.files[0]);

            $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm mr-1"></i>Uploading...');

            $.ajax({
                url: "{{ route('releases.cr-attachments.store', ':id') }}".replace(':id', releaseId),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, 'Success');
                        var a = response.attachment;

                        // Remove empty row
                        $('.attachment-no-records').remove();

                        // Add new row
                        var typeLabel = a.type && a.type !== '-' ? '<span class="label label-light-info label-inline font-weight-bold">' + a.type + '</span>' : '<span class="text-muted">-</span>';
                        var newRow = '<tr data-attachment-id="' + a.id + '">' +
                            '<td class="font-weight-bolder text-primary">#' + a.cr_no + '</td>' +
                            '<td><a href="' + a.file_url + '" target="_blank" class="text-hover-primary font-weight-bold">' +
                                '<i class="la la-file-download mr-1 text-info"></i>' + a.file_name + '</a></td>' +
                            '<td>' + typeLabel + '</td>' +
                            '<td>' + (a.description || '-') + '</td>' +
                            '<td class="text-muted">' + a.updated_by + '</td>' +
                            '<td class="text-muted font-size-sm">' + a.updated_at + '</td>' +
                            '<td class="text-center">' +
                                '<button type="button" class="btn btn-icon btn-light-danger btn-sm js-remove-cr-attachment" ' +
                                'data-attachment-id="' + a.id + '" data-file-name="' + a.file_name + '" title="Delete Attachment">' +
                                '<i class="la la-trash"></i></button>' +
                            '</td></tr>';
                        $('#cr_attachments_table_body').append(newRow);

                        // Update count
                        var count = $('#cr_attachments_table_body tr:not(.attachment-no-records)').length;
                        $('#cr_attachments_count').text(count + ' Files');

                        // Clear form
                        $('#cr_attachment_cr_id').val('').trigger('change');
                        $('#cr_attachment_type').val('');
                        $('#cr_attachment_description').val('');
                        $('#cr_attachment_file').val('');
                        $('#cr_attachment_file').next('.custom-file-label').html('Choose file...');
                        $message.text('Attachment uploaded successfully.').addClass('text-success');
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.message || 'Failed to upload attachment.';
                    if (xhr.responseJSON?.errors) {
                        var errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errors.join(' ');
                    }
                    $message.text(errorMsg).addClass('text-danger');
                    toastr.error(errorMsg, 'Error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="la la-upload mr-1"></i>Upload');
                }
            });
        });

        // Delete CR Attachment
        $(document).on('click', '.js-remove-cr-attachment', function() {
            var $btn = $(this);
            var attachmentId = $btn.data('attachment-id');
            var fileName = $btn.data('file-name');

            Swal.fire({
                title: 'Delete ' + fileName + '?',
                text: 'This will permanently delete this attachment.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F64E60',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="la la-trash"></i> Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i>');
                    $.ajax({
                        url: "{{ route('releases.cr-attachments.destroy', [':release', ':attachment']) }}".replace(':release', releaseId).replace(':attachment', attachmentId),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message, 'Success');
                                $btn.closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    var count = $('#cr_attachments_table_body tr:not(.attachment-no-records)').length;
                                    $('#cr_attachments_count').text(count + ' Files');
                                    if (count === 0) {
                                        $('#cr_attachments_table_body').html(
                                            '<tr class="attachment-no-records"><td colspan="7" class="text-center text-muted font-weight-bold py-8">' +
                                            '<i class="la la-paperclip text-muted font-size-h1 d-block mb-3"></i>No attachments uploaded yet.</td></tr>'
                                        );
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            var errorMsg = xhr.responseJSON?.message || 'Failed to delete attachment.';
                            toastr.error(errorMsg, 'Error');
                            $btn.prop('disabled', false).html('<i class="la la-trash"></i>');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
