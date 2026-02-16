@php
    $isEdit = isset($row);
@endphp

<div class="card-body">
    @if($errors->any())
        <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon-warning"></i></div>
            <div class="alert-text">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Section: Release Information -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Release Information</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @if($isEdit)
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Release ID #</label>
                        <input type="text" class="form-control-plaintext" value="{{ $row->id }}" disabled>
                    </div>
                </div>
                @endif
                <div class="col-md-{{ $isEdit ? '8' : '12' }}">
                    <div class="form-group">
                        <label class="font-weight-bold">Release Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $row->name ?? old('name') }}" required placeholder="Enter Release Name">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Vendor Name <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="vendor_id" required>
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ (isset($row) && $row->vendor_id == $vendor->id) ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Priority <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="priority_id" required>
                            <option value="">Select Priority</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}" {{ (isset($row) && $row->priority_id == $priority->id) ? 'selected' : '' }}>{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Creator Information -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Creator Information</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Creator RTM Name</label>
                        <input type="text" class="form-control" name="creator_rtm_name" value="{{ $row->creator_rtm_name ?? auth()->user()->name }}" readonly style="background-color: #f3f6f9;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">RTM Email</label>
                        <input type="email" class="form-control" name="rtm_email" value="{{ $row->rtm_email ?? auth()->user()->email }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Details -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Details</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="font-weight-bold">Release Description</label>
                <textarea class="form-control" name="release_description" rows="4">{{ $row->release_description ?? old('release_description') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Section: Dates -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Timeline</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Release Start Date</label>
                        <input type="date" class="form-control" name="release_start_date" value="{{ $row->release_start_date ?? old('release_start_date') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Go Live Planned Date</label>
                        <input type="date" class="form-control" name="go_live_planned_date" value="{{ $row->go_live_planned_date ?? old('go_live_planned_date') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Attachments & Feedback -->
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Technical Information</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="font-weight-bold">Technical Attachment</label>
                <input type="file" class="form-control" name="technical_attachment">
            </div>
            <div class="form-group">
                <label class="font-weight-bold">Technical Feedback</label>
                <textarea class="form-control" name="technical_feedback" rows="3" placeholder="Enter feedback..."></textarea>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        $('.select2').select2({ placeholder: "Select an option", allowClear: true, width: '100%' });
    });
</script>
@endpush
