{{-- Release History Logs - Loaded via AJAX into modal --}}
<div class="timeline timeline-6 mt-3">
    @forelse($logs as $key => $log)
        <div class="timeline-item align-items-start">
            {{-- Label --}}
            <div class="timeline-label font-weight-bold text-primary font-size-lg">
                {{ $log->created_at->format('d M Y') }} {{ $log->created_at->format('h:i A') }}
            </div>

            {{-- Badge --}}
            <div class="timeline-badge">
                <i class="fa fa-genderless text-{{ $key % 2 == 0 ? 'primary' : 'success' }} icon-xl"></i>
            </div>

            {{-- Content --}}
            <div class="timeline-content d-flex flex-column pl-3">
                <span class="font-weight-bolder text-dark-75">
                    {{ $log->log_text }}
                </span>
                <span class="text-muted font-size-sm mt-1">
                    By: <span class="text-primary font-weight-bold">{{ $log->user->name ?? 'Unknown' }}</span> 
                </span>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="la la-history text-muted" style="font-size: 48px; opacity: 0.3;"></i>
            <p class="text-muted font-weight-bold mt-3">No logs available for this release.</p>
        </div>
    @endforelse
</div>