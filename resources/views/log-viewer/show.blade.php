@extends('layouts.app')

@section('content')

    @push('css')
        <title>TMS Log Viewer - Log #{{ $log->id }}</title>
        <style>
            .log-header {
                background: white;
                border: 1px solid #e9ecef;
                border-radius: 8px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
                max-height: 600px;
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
                min-width: 150px;
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
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .trace-item code {
                font-size: 0.85rem;
            }
        </style>
    @endpush

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">
                            <i class="fas fa-file-alt mr-2"></i>Log Details - #{{ $log->id }}
                        </h2>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('log-viewer.index') }}" class="btn btn-sm btn-light-primary">
                                <i class="fas fa-arrow-left mr-1"></i>Back to Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Subheader-->

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <div class="container">

                <!-- Log Header -->
                <div class="log-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="mb-3">
                                <span class="font-weight-bold mr-2"
                                      style="font-size: 1.25rem;">ID: {{ $log->id }}</span>

                                @if($log->level_name === 'ERROR')
                                    <span class="badge badge-danger badge-lg">{{ $log->level_name }}</span>
                                @elseif($log->level_name === 'WARNING')
                                    <span class="badge badge-warning badge-lg">{{ $log->level_name }}</span>
                                @elseif($log->level_name === 'INFO')
                                    <span class="badge badge-info badge-lg">{{ $log->level_name }}</span>
                                @else
                                    <span class="badge badge-secondary badge-lg">{{ $log->level_name }}</span>
                                @endif

                                @if($log->needsResolution())
                                    <span class="badge badge-danger badge-lg ml-1">Unsolved</span>
                                @elseif($log->level_name === 'ERROR')
                                    <span class="badge badge-success badge-lg ml-1">Solved</span>
                                @endif
                            </div>

                             <div class="text-muted mb-2 d-flex align-items-center">
                                 <i class="far fa-clock"></i> <span class="ml-1">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                 <span class="ml-3 d-flex align-items-center"><i class="fas fa-network-wired"></i> <span class="ml-1">{{ $log->ip_address ?? 'N/A' }}</span></span>
                                 <span class="ml-3 d-flex align-items-center"><i class="fas fa-fingerprint"></i> <span class="ml-1">{{ $log->log_hash }}</span></span>
                             </div>

                            <div class="text-dark font-weight-bold" style="font-size: 1.1rem;">{{ $log->message }}</div>
                        </div>

                        <div class="ml-3 d-flex align-items-center">
                            @if($log->needsResolution())
                                <form method="POST" action="{{ route('log-viewer.resolve', $log->id) }}"
                                      class="d-inline resolve-form mr-1">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-success" onclick="confirmResolve(this)"
                                            title="Mark as Solved">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('log-viewer.resolve-similar', $log->id) }}"
                                      class="d-inline resolve-similar-form mr-1">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-info"
                                            onclick="confirmResolveSimilar(this)" title="Solve All Similar">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('log-viewer.destroy', $log->id) }}"
                                  class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(this)"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Log Details -->
                <div class="card card-custom">
                    <div class="card-body">
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
                                            <div class="detail-value">{{ $log->solver?->name ?? 'N/A' }}
                                                at {{ $log->solved_at?->format('Y-m-d H:i:s') }}</div>
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

                            <!-- Headers -->
                            <div class="col-md-12">
                                <div class="detail-box">
                                    <h6><i class="fas fa-list mr-2"></i>Headers</h6>
                                    <pre>{{ json_encode($log->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>

                            <!-- Context -->
                            <div class="col-md-12">
                                <div class="detail-box">
                                    <h6><i class="fas fa-code mr-2"></i>Context</h6>
                                    <pre>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>

                            <!-- Stack Trace -->
                            @if($log->trace_stack && is_array($log->trace_stack))
                                <div class="col-md-12">
                                    <div class="detail-box">
                                        <h6><i class="fas fa-bug mr-2"></i>Stack Trace</h6>
                                        @foreach($log->trace_stack as $exceptionKey => $traces)
                                            @if(is_array($traces))
                                                <div class="mb-3">
                                                    <div
                                                        class="font-weight-bold text-danger mb-2">{{ ucfirst($exceptionKey) }}</div>
                                                    @foreach($traces as $index => $trace)
                                                        <div class="trace-item mb-2 p-3"
                                                             style="background: #f8f9fa; border-left: 3px solid #dc3545; border-radius: 4px;">
                                                            <div class="d-flex align-items-start">
                                                                <span class="badge badge-secondary mr-2"
                                                                      style="min-width: 30px;">#{{ $index }}</span>
                                                                <div class="flex-grow-1">
                                                                    @if(isset($trace['file']))
                                                                        <div class="text-primary font-weight-bold mb-1">
                                                                            <i class="fas fa-file-code mr-1"></i>
                                                                            {{ basename($trace['file']) }}
                                                                            @if(isset($trace['line']))
                                                                                <span
                                                                                    class="text-muted">: {{ $trace['line'] }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-muted small mb-1"
                                                                             style="font-family: monospace; font-size: 0.75rem;">
                                                                            {{ $trace['file'] }}
                                                                        </div>
                                                                    @endif

                                                                    @if(isset($trace['class']) || isset($trace['function']))
                                                                        <div class="text-dark mt-2">
                                                                            <i class="fas fa-code mr-1"></i>
                                                                            <code
                                                                                style="background: white; padding: 2px 6px; border-radius: 3px;">
                                                                                @if(isset($trace['class']))
                                                                                    <span
                                                                                        class="text-info">{{ $trace['class'] }}</span>
                                                                                @endif
                                                                                @if(isset($trace['type']))
                                                                                    <span
                                                                                        class="text-muted">{{ $trace['type'] }}</span>
                                                                                @endif
                                                                                @if(isset($trace['function']))
                                                                                    <span
                                                                                        class="text-success">{{ $trace['function'] }}</span>
                                                                                    <span class="text-muted">()</span>
                                                                                @endif
                                                                            </code>
                                                                        </div>
                                                                    @endif

                                                                    @if(isset($trace['args']) && is_array($trace['args']) && count($trace['args']) > 0)
                                                                        <div class="mt-2">
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-list mr-1"></i>
                                                                                Arguments: {{ count($trace['args']) }}
                                                                            </small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('public/new_theme/sweetalert2.min.js')}}"></script>
    <script>
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

@endsection
