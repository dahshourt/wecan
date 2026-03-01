@extends('layouts.app')

@section('content')

@push('css')
<style>
    .stats-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .stats-card h6 {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        color: #6c757d;
    }

    .stats-card .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
    }

    .log-row {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .log-row:hover {
        background: #f8f9fa;
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .log-row.active {
        background: #f8f9fa;
        border-color: #667eea;
    }

    .log-details-section {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .detail-box {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .detail-box h6 {
        color: #495057;
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .detail-box pre {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 0.75rem;
        margin: 0;
        color: #495057;
        font-size: 0.8rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .detail-item {
        display: flex;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #6c757d;
        font-weight: 600;
        min-width: 120px;
    }

    .detail-value {
        color: #495057;
        word-break: break-all;
    }

    .trace-item {
        transition: all 0.2s;
    }

    .trace-item:hover {
        background: #e9ecef !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .trace-item code {
        font-size: 0.85rem;
    }

    /* Style form inputs to match Select2 */
    .form-control {
        height: 38px;
        border: 1px solid #d1d3e2;
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-control::placeholder {
        color: #999;
        opacity: 1;
    }

    /* Style buttons */
    .btn {
        height: 38px;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.375rem 1rem;
        transition: all 0.15s ease-in-out;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        border: none;
    }

    .btn-secondary:hover {
        background: #5a6268;
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }

    /* Adjust Select2 to match */
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #d1d3e2 !important;
        border-radius: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        color: #495057 !important;
        padding-left: 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #999 !important;
    }
</style>
@endpush

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <!-- Page Title -->
            <div class="card card-custom mb-4 mt-3" style="background: white; border: 1px solid #e9ecef; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div class="card-body py-4 px-5">
                    <div class="d-flex align-items-center">
                        <div class="mr-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 60px; height: 60px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clipboard-list text-white" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="font-weight-bolder mb-2" style="font-size: 1.75rem; color: #2d3748;">
                                TMS Log Viewer
                            </h2>
                            <p class="mb-0" style="font-size: 0.95rem; color: #495057;">
                                Monitor and manage system logs, track errors, and maintain application health
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card">
                        <h6>Total Logs</h6>
                        <div class="stats-number">{{ $logs->total() }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h6>Unsolved Errors</h6>
                        <div class="stats-number text-danger">{{ $statistics['unresolved_errors'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card card-custom mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('log-viewer.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="font-weight-bold">Log Level</label>
                                <select name="level" class="form-control kt-select2" id="log-level-filter">
                                    <option value="">All Levels</option>
                                    @foreach($statistics['available_levels'] as $level)
                                        <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                            {{ $level }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="font-weight-bold">Status</label>
                                <select name="status" class="form-control kt-select2" id="status-filter">
                                    <option value="">All Status</option>
                                    <option value="unresolved" {{ request('status') == 'unresolved' ? 'selected' : '' }}>Unsolved</option>
                                    <option value="solved" {{ request('status') == 'solved' ? 'selected' : '' }}>Solved</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="font-weight-bold">Date</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="font-weight-bold">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter"></i> Apply
                                </button>
                                <a href="{{ route('log-viewer.index') }}" class="btn btn-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logs List -->
            <div class="card card-custom">
                <div class="card-body p-3">
                    @if($logs->count() > 0)
                        @foreach($logs as $log)
                            <div class="log-row" id="log-{{ $log->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1" onclick="toggleDetails({{ $log->id }})" style="cursor: pointer;">
                                        <div class="mb-2">
                                            @if($log->level_name === 'ERROR')
                                                <span class="badge badge-danger">{{ $log->level_name }}</span>
                                            @elseif($log->level_name === 'WARNING')
                                                <span class="badge badge-warning">{{ $log->level_name }}</span>
                                            @elseif($log->level_name === 'INFO')
                                                <span class="badge badge-info">{{ $log->level_name }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $log->level_name }}</span>
                                            @endif

                                            @if($log->needsResolution())
                                                <span class="badge badge-danger ml-1">Unsolved</span>
                                            @elseif($log->level_name === 'ERROR')
                                                <span class="badge badge-success ml-1">Solved</span>
                                            @endif
                                        </div>

                                        <div class="text-muted small mb-1 d-flex align-items-center">
                                            <i class="far fa-clock"></i> <span class="ml-1">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                            <span class="ml-3 d-flex align-items-center"><i class="fas fa-network-wired"></i> <span class="ml-1">{{ $log->ip_address ?? 'N/A' }}</span></span>
                                            <span class="ml-3 d-flex align-items-center"><i class="fas fa-fingerprint"></i> <span class="ml-1">{{ $log->log_hash }}</span></span>
                                        </div>

                                        <div class="text-dark">{{ $log->message }}</div>
                                    </div>

                                    <div class="ml-3 d-flex align-items-center">
                                        @if($log->needsResolution())
                                            <form method="POST" action="{{ route('log-viewer.resolve', $log->id) }}" class="d-inline resolve-form mr-1" onclick="event.stopPropagation()">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-success" onclick="confirmResolve(this)" title="Mark as Solved">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('log-viewer.resolve-similar', $log->id) }}" class="d-inline resolve-similar-form mr-1" onclick="event.stopPropagation()">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-info" onclick="confirmResolveSimilar(this)" title="Solve All Similar">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('log-viewer.show', $log->id) }}" class="btn btn-sm btn-primary mr-1 d-inline-flex align-items-center justify-content-center" onclick="event.stopPropagation()" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('log-viewer.destroy', $log->id) }}" class="d-inline delete-form mr-1" onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(this)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-light" onclick="toggleDetails({{ $log->id }})" title="Toggle Details">
                                            <i class="fas fa-chevron-down" id="icon-{{ $log->id }}"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Details Section -->
                                <div class="log-details-section" id="details-{{ $log->id }}" style="display: none;">
                                    <div class="row">
                                        <!-- Request Information -->
                                        <div class="col-md-6">
                                            <div class="detail-box">
                                                <h6><i class="fas fa-globe mr-2"></i>Request Information</h6>
                                                <div class="detail-item">
                                                    <div class="detail-label">IP Address</div>
                                                    <div class="detail-value">{{ $log->ip_address ?? 'N/A' }}</div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">HTTP Method</div>
                                                    <div class="detail-value">{{ $log->http_method ?? 'N/A' }}</div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">URL</div>
                                                    <div class="detail-value">{{ $log->url ?? 'N/A' }}</div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Referer</div>
                                                    <div class="detail-value">{{ $log->referer_url ?? 'N/A' }}</div>
                                                </div>
                                                @if($log->solved && $log->level_name === 'ERROR')
                                                <div class="detail-item">
                                                    <div class="detail-label">Solved By</div>
                                                    <div class="detail-value">{{ $log->solver->name ?? 'N/A' }} at {{ $log->solved_at?->format('Y-m-d H:i:s') }}</div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- User Agent -->
                                        <div class="col-md-6">
                                            <div class="detail-box">
                                                <h6><i class="fas fa-desktop mr-2"></i>User Agent</h6>
                                                <div class="detail-value">{{ $log->user_agent ?? 'N/A' }}</div>
                                            </div>

                                            <!-- User Information -->
                                            @if(isset($log->context['user_name']) || isset($log->context['user_email']) || isset($log->context['user_group']))
                                            <div class="detail-box mt-3">
                                                <h6><i class="fas fa-user mr-2"></i>User Information</h6>
                                                @if(isset($log->context['user_name']))
                                                <div class="detail-item">
                                                    <div class="detail-label">Name</div>
                                                    <div class="detail-value">{{ $log->context['user_name'] }}</div>
                                                </div>
                                                @endif
                                                @if(isset($log->context['user_email']))
                                                <div class="detail-item">
                                                    <div class="detail-label">Email</div>
                                                    <div class="detail-value">{{ $log->context['user_email'] }}</div>
                                                </div>
                                                @endif
                                                @if(isset($log->context['user_group']))
                                                <div class="detail-item">
                                                    <div class="detail-label">Group</div>
                                                    <div class="detail-value">{{ $log->context['user_group'] }}</div>
                                                </div>
                                                @endif
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Context -->
                                        <div class="col-md-12">
                                            <div class="detail-box">
                                                <h6><i class="fas fa-code mr-2"></i>Context</h6>
                                                <pre>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No logs found</h4>
                            <p class="text-muted">Try adjusting your filters</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script src="{{asset('public/new_theme/sweetalert2.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for filters
        $('.kt-select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });
    });

    function toggleDetails(logId) {
        const details = document.getElementById('details-' + logId);
        const logRow = document.getElementById('log-' + logId);
        const icon = document.getElementById('icon-' + logId);

        if (details.style.display === 'none') {
            details.style.display = 'block';
            logRow.classList.add('active');
            if (icon) {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        } else {
            details.style.display = 'none';
            logRow.classList.remove('active');
            if (icon) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }

    function confirmResolve(button) {
        event.stopPropagation();

        Swal.fire({
            title: 'Mark as Solved?',
            text: "This will mark this log entry as resolved.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, mark as solved',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loader
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we mark this log as solved',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                button.closest('form').submit();
            }
        });
    }

    function confirmResolveSimilar(button) {
        event.stopPropagation();

        Swal.fire({
            title: 'Solve All Similar?',
            text: "This will mark all logs with the same error hash as resolved.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, solve all similar',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loader
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we mark all similar logs as solved',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                button.closest('form').submit();
            }
        });
    }

    function confirmDelete(button) {
        event.stopPropagation();

        Swal.fire({
            title: 'Delete Log?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loader
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete this log',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                button.closest('form').submit();
            }
        });
    }
</script>
@endpush

@endsection
