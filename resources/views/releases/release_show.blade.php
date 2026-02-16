@extends('layouts.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex flex-column">
                    <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                   <div class="card-title">
                        <h3 class="card-label">Release Details: {{ $row->name }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('releases.edit', $row->id) }}" class="btn btn-warning font-weight-bolder mr-2">
                            <i class="la la-edit"></i> Edit
                        </a>
                        <a href="{{ route('releases.index') }}" class="btn btn-secondary font-weight-bolder">
                            <i class="la la-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- General Info -->
                    <div class="row mb-5">
                       <div class="col-md-6">
                           <label class="font-weight-bold">Status:</label>
                           <span class="label label-lg label-light-primary label-inline font-weight-bold ml-2">{{ $row->status->status_name ?? 'N/A' }}</span>
                       </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Vendor Name:</label>
                                <p class="form-control-plaintext text-muted">{{ $row->vendor_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Priority:</label>
                                <p class="form-control-plaintext text-muted">{{ $row->priority ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Release Start Date:</label>
                                <p class="form-control-plaintext text-muted">{{ $row->release_start_date ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="font-weight-bold">Go Live Planned Date:</label>
                                <p class="form-control-plaintext text-muted">{{ $row->go_live_planned_date ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Description:</label>
                                <p class="form-control-plaintext text-muted">{{ $row->release_description ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="separator separator-dashed my-8"></div>
                    
                    <!-- Creator Info -->
                    <h4 class="font-weight-bold mb-4">Creator Information</h4>
                     <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Creator RTM Name:</label>
                                <p class="form-control-plaintext text-muted">{{ $row->creator_rtm_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="font-weight-bold">RTM Email:</label>
                                <p class="form-control-plaintext text-muted">{{ $row->rtm_email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Feedback Table -->
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Technical Feedback</h3>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Assuming feedback logs are stored in ReleaseLogs or similar, need to confirm exact source. 
                         For now, I will display ReleaseLogs filtered or just all logs if no specific feedback table exists. 
                         The user asked for 'Table like CR Page'. I'll adapt the structure. -->
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center" id="feedback_table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Feedback/Log</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($logs))
                                    @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->user->user_name ?? 'System' }}</td> 
                                        <!-- Assuming ReleaseLogs has user method and log_text -->
                                        <td>{{ $log->log_text }}</td>
                                        <td>{{ $log->created_at }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Attachments Section -->
            <div class="card card-custom gutter-b">
                 <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Technical Attachments</h3>
                    </div>
                    <div class="card-toolbar">
                         <!-- Trigger Modal or Inline Form? CR page uses inline dropzone. -->
                    </div>
                </div>
                <div class="card-body">
                     <!-- Upload Form -->
                     <form class="form mb-4" id="attachmentForm">
                        <div class="form-group">
                             <label>Upload Attachment:</label>
                             <div class="dropzone" id="kt_dropzone_3">
                                 <div class="dropzone-msg dz-message needsclick">
                                    <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                                </div>
                             </div>
                        </div>
                     </form>

                     <!-- Attachments List -->
                     <div class="table-responsive">
                         <table class="table table-bordered">
                             <thead>
                                 <tr>
                                     <th>File Name</th>
                                     <th>Uploaded By</th>
                                     <th>Date</th>
                                     <th>Action</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @forelse($row->attachments ?? collect() as $attachment)
                                 <tr>
                                     <td>{{ $attachment->file_name }}</td>
                                     <td>{{ $attachment->user->name ?? 'Unknown' }}</td>
                                     <td>{{ $attachment->created_at }}</td>
                                     <td>
                                         <a href="{{ route('releases.attachments.download', $attachment->id) }}" class="btn btn-sm btn-light-primary font-weight-bold">
                                             <i class="la la-download"></i> Download
                                         </a>
                                     </td>
                                 </tr>
                                 @empty
                                 <tr>
                                     <td colspan="4" class="text-center text-muted">No attachments uploaded.</td>
                                 </tr>
                                 @endforelse
                             </tbody>
                         </table>
                     </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Dropzone Logic
    $('#kt_dropzone_3').dropzone({
        url: "{{ url('releases/' . $row->id . '/upload-attachment') }}",
        paramName: "file", 
        maxFiles: 10,
        maxFilesize: 10, // MB
        addRemoveLinks: true,
        acceptedFiles: "image/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        sending: function(file, xhr, formData) {
            formData.append("release_id", "{{ $row->id }}");
        },
        success: function(file, response) {
            // Reload page or append row
            location.reload(); 
        }
    });
</script>
@endsection
