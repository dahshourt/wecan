{{-- Release Tab Content --}}
@php
    // Determine if release is in editable status (Planned or Development)
    $editableStatuses = ['Planned', 'Development'];
    $currentStatusName = $row->releaseStatusObj->name ?? $row->status->name ?? '';
    $isEditableStatus = in_array($currentStatusName, $editableStatuses);
@endphp

{{-- Section 1: Release Information (Always Read-only) --}}
<div class="mb-5">
    <h4 class="font-weight-bold text-dark mb-3">
        <i class="la la-users text-primary mr-2"></i>Release Information
    </h4>
    <p class="text-muted">Release Information</p>
</div>
<div class="card card-custom card-border shadow-sm mb-6">
    <div class="card-header bg-light-primary py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-info-circle text-primary mr-2"></i>Release Information
            </h3>
        </div>
        <div class="card-toolbar">
            <span class="font-weight-bold text-primary">
                {{ $currentStatusName ?: 'N/A' }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="text-muted font-size-sm">Release ID #</label>
                    <div class="font-weight-bolder font-size-lg text-primary">#{{ $row->id }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="text-muted font-size-sm">Release Name</label>
                    <div class="font-weight-bolder font-size-lg">{{ $row->name ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="text-muted font-size-sm">Vendor Name</label>
                    <div class="font-weight-bolder font-size-lg">{{ $row->vendor->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="text-muted font-size-sm">Target System</label>
                    <div class="font-weight-bolder">{{ $row->targetSystem->name ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="text-muted font-size-sm">Creator RTM Name</label>
                    <div class="font-weight-bolder">{{ $row->creator_rtm_name ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
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
</div>

{{-- Section 2: Planning Details (Conditional Editing) --}}
<div class="card card-custom card-border shadow-sm mb-6">
    <div class="card-header bg-light-info py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-calendar-alt text-info mr-2"></i>Planning Details
            </h3>
        </div>
        @if(!$isEditableStatus)
            <div class="card-toolbar">
                <span class="label label-light-danger label-inline">
                    <i class="la la-lock mr-1"></i> Read Only
                </span>
            </div>
        @endif
    </div>
    <div class="card-body">
        {{-- Description --}}
        <div class="form-group mb-5">
            <label class="font-weight-bold">Release Description</label>
            @if($isEditableStatus)
                <textarea class="form-control" name="release_description" rows="3" 
                          placeholder="Enter release description...">{{ $row->release_description }}</textarea>
            @else
                <div class="form-control-plaintext bg-light rounded p-3">
                    {{ $row->release_description ?: 'No description provided.' }}
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold">Priority</label>
                    @if($isEditableStatus)
                        <select class="form-control select2" name="priority_id">
                            <option value="">Select Priority</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}" {{ ($row->priority_id == $priority->id) ? 'selected' : '' }}>
                                    {{ $priority->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="form-control-plaintext">
                            <span class="label label-light-primary label-lg font-weight-bold">
                                {{ $row->priority->name ?? 'N/A' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold">Release Start Date</label>
                    @if($isEditableStatus)
                        <input type="date" class="form-control" name="release_start_date" 
                               value="{{ $row->release_start_date }}">
                    @else
                        <div class="form-control-plaintext font-weight-bolder">
                            {{ $row->release_start_date ? \Carbon\Carbon::parse($row->release_start_date)->format('d M Y') : 'N/A' }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold">Go Live Planned Date</label>
                    @if($isEditableStatus)
                        <input type="date" class="form-control" name="go_live_planned_date" 
                               value="{{ $row->go_live_planned_date }}">
                    @else
                        <div class="form-control-plaintext font-weight-bolder">
                            {{ $row->go_live_planned_date ? \Carbon\Carbon::parse($row->go_live_planned_date)->format('d M Y') : 'N/A' }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold">Responsible RTM</label>
                    @if($isEditableStatus)
                        <select class="form-control select2" name="responsible_rtm_id">
                            <option value="">Select RTM</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ ($row->responsible_rtm_id == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="form-control-plaintext font-weight-bolder">
                            {{ $row->responsibleRtm->name ?? 'N/A' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Section 3: Testing Schedule (Only for Planned/Development) --}}
@if($isEditableStatus)
<div class="card card-custom card-border shadow-sm mb-6">
    <div class="card-header bg-light-warning py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-tasks text-warning mr-2"></i>Testing Schedule
            </h3>
        </div>
    </div>
    <div class="card-body">
        {{-- ATP Review & Vendor Internal Test --}}
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="bg-light rounded p-4">
                    <h6 class="font-weight-bolder text-dark mb-4">
                        <i class="la la-file-alt text-primary mr-2"></i>ATP Review
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="atp_review_start_date" 
                                       value="{{ $row->atp_review_start_date }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="atp_review_end_date" 
                                       value="{{ $row->atp_review_end_date }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-light rounded p-4">
                    <h6 class="font-weight-bolder text-dark mb-4">
                        <i class="la la-flask text-info mr-2"></i>Vendor Internal Test
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="vendor_internal_test_start_date" 
                                       value="{{ $row->vendor_internal_test_start_date }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="vendor_internal_test_end_date" 
                                       value="{{ $row->vendor_internal_test_end_date }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- IOT & E2E --}}
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="bg-light rounded p-4">
                    <h6 class="font-weight-bolder text-dark mb-4">
                        <i class="la la-cogs text-success mr-2"></i>IOT (Integration Operational Testing)
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="iot_start_date" 
                                       value="{{ $row->iot_start_date }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="iot_end_date" 
                                       value="{{ $row->iot_end_date }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-light rounded p-4">
                    <h6 class="font-weight-bolder text-dark mb-4">
                        <i class="la la-project-diagram text-warning mr-2"></i>E2E (End-to-End Testing)
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="e2e_start_date" 
                                       value="{{ $row->e2e_start_date }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="e2e_end_date" 
                                       value="{{ $row->e2e_end_date }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- UAT & Smoke Test --}}
        <div class="row">
            <div class="col-md-6">
                <div class="bg-light rounded p-4">
                    <h6 class="font-weight-bolder text-dark mb-4">
                        <i class="la la-user-check text-primary mr-2"></i>UAT (User Acceptance Testing)
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="uat_start_date" 
                                       value="{{ $row->uat_start_date }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="uat_end_date" 
                                       value="{{ $row->uat_end_date }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-light rounded p-4">
                    <h6 class="font-weight-bolder text-dark mb-4">
                        <i class="la la-fire text-danger mr-2"></i>Smoke Test
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="smoke_test_start_date" 
                                       value="{{ $row->smoke_test_start_date }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="font-size-sm">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="smoke_test_end_date" 
                                       value="{{ $row->smoke_test_end_date }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Section 4: Technical Information (Always Editable) --}}
<div class="card card-custom card-border shadow-sm mb-6">
    <div class="card-header bg-light-success py-4">
        <div class="card-title">
            <h3 class="card-label font-weight-bolder text-dark">
                <i class="la la-file-upload text-success mr-2"></i>Technical Information
            </h3>
        </div>
    </div>
    <div class="card-body">
        {{-- Attachment Upload --}}
        <div class="form-group mb-5">
            <label class="font-weight-bold">Upload Technical Attachment</label>
            <div class="input-group">
                <div class="custom-file" id="technicalAttachmentWrapper">
                    <input type="file" class="custom-file-input" name="technical_attachment" id="technicalAttachment" style="pointer-events: none;">
                    <label class="custom-file-label" for="technicalAttachment" id="technicalAttachmentLabel">Choose file...</label>
                </div>
            </div>
        </div>

        {{-- Existing Attachments --}}
        @if($row->attachments && $row->attachments->count() > 0)
            <div class="mb-5">
                <label class="font-weight-bold d-block mb-3">Existing Attachments</label>
                @foreach($row->attachments as $attachment)
                    <div class="d-flex align-items-center bg-light rounded p-3 mb-2">
                        <div class="symbol symbol-35 symbol-light-primary mr-3 flex-shrink-0">
                            <span class="symbol-label">
                                <i class="la la-file-alt text-primary icon-lg"></i>
                            </span>
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
            </div>
        @endif

        <div class="separator separator-dashed my-5"></div>

        {{-- Feedback --}}
        <div class="form-group mb-0">
            <label class="font-weight-bold">Technical Feedback</label>
            <textarea class="form-control" name="technical_feedback" rows="3" 
                      placeholder="Add new feedback..."></textarea>
            
            @if($row->feedbacks && $row->feedbacks->count() > 0)
                <div class="mt-5">
                    <label class="text-muted font-size-sm mb-3">Feedback History:</label>
                    <div class="timeline timeline-3">
                        @foreach($row->feedbacks->sortByDesc('created_at') as $feedback)
                            <div class="timeline-item mb-3">
                                <div class="timeline-media bg-light-primary">
                                    <i class="la la-comment text-primary"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="bg-light rounded p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="font-weight-bolder text-dark">
                                                {{ $feedback->creator->name ?? 'Unknown' }}
                                            </span>
                                            <span class="text-muted font-size-sm">
                                                {{ $feedback->created_at->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                        <p class="mb-0">{{ $feedback->feedback }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('script')
<script>
    // Override global KTApp handler — show only filename, not full path
    $(document).ready(function() {
        // Unbind any global handlers on this specific input
        $('#technicalAttachment').off('change');
        
        // Re-enable pointer events (we disabled them briefly to prevent double-binding)
        $('#technicalAttachment').css('pointer-events', 'auto');
        
        // Bind our own clean handler
        $('#technicalAttachment').on('change', function(e) {
            e.stopImmediatePropagation();
            var fileName = this.files && this.files.length > 0 ? this.files[0].name : 'Choose file...';
            $('#technicalAttachmentLabel').text(fileName);
        });
    });
</script>
@endpush

