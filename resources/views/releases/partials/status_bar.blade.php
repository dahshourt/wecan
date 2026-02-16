{{-- Professional Release Header + Status Progress --}}
@php
    $allStatuses = \App\Models\ReleaseStatus::getOrdered();
    $currentStatusId = $release->release_status_id ?? null;
    $currentStatus = $allStatuses->firstWhere('id', $currentStatusId);
    $currentOrder = $currentStatus ? $currentStatus->display_order : 0;
    $totalStatuses = $allStatuses->count();
    $currentStatusName = $currentStatus->name ?? 'N/A';
@endphp

{{-- Release Hero Header --}}
<div class="card card-custom gutter-b shadow-sm" >
    <div class="card-body py-5">
        {{-- Top Row: Release ID, Name, Back Button --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-50 symbol-circle symbol-light-primary mr-4 flex-shrink-0">
                    <span class="symbol-label font-size-h5 font-weight-bolder">
                        R#{{ $release->id }}
                    </span>
                </div>
                <div>
                    <h3 class="font-weight-bolder text-dark mb-0" style="font-size: 1.4rem;">
                        {{ $release->name }}
                    </h3>
                    <div class="d-flex align-items-center mt-1">
                        <span class="text-muted font-size-xs">
                            <i class="la la-calendar-alt mr-1"></i>
                            {{ $release->created_at ? $release->created_at->format('d M Y') : '' }}
                        </span>
                    </div>
                </div>
            </div>
            <a href="{{ route('releases.index') }}" class="btn btn-light-primary btn-sm font-weight-bolder">
                <i class="la la-arrow-left"></i> Back to List
            </a>
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

                        if ($isPassed) {
                            $dotColor = '#1BC5BD'; // Green
                            $dotBorder = '#1BC5BD';
                            $labelColor = '#1BC5BD';
                            $labelWeight = '500';
                        } elseif ($isActive) {
                            $dotColor = '#6f42c1'; // Orange
                            $dotBorder = '#6f42c1';
                            $labelColor = '#6f42c1';
                            $labelWeight = '700';
                        } else {
                            $dotColor = '#E4E6EF'; // Gray
                            $dotBorder = '#D1D3E0';
                            $labelColor = '#B5B5C3';
                            $labelWeight = '400';
                        }

                        $progressPercent = $totalStatuses > 1 ? ($index / ($totalStatuses - 1)) * 100 : 0;
                    @endphp
                    <div class="stepper-step text-center" style="z-index: 1; flex: 1;">
                        {{-- Dot --}}
                        <div class="stepper-dot mx-auto {{ $isActive ? 'active-pulse' : '' }}"
                             style="width: {{ $isActive ? '18px' : '14px' }}; 
                                    height: {{ $isActive ? '18px' : '14px' }}; 
                                    border-radius: 50%; 
                                    background-color: {{ $dotColor }}; 
                                    border: 2px solid {{ $dotBorder }};
                                    {{ $isPassed ? 'box-shadow: 0 0 0 3px rgba(27,197,189,0.15);' : '' }}
                                    {{ $isActive ? 'box-shadow: 0 0 0 4px rgba(255,168,0,0.25);' : '' }}
                                    transition: all 0.3s ease;">
                            @if($isPassed)
                                <i class="fa fa-check" style="font-size: 8px; color: #fff; line-height: {{ $isActive ? '14px' : '10px' }}; display: block; text-align: center;"></i>
                            @endif
                        </div>
                        {{-- Label --}}
                        <div class="stepper-label mt-2" 
                             style="font-size: 10px; 
                                    font-weight: {{ $labelWeight }}; 
                                    color: {{ $labelColor }};
                                    line-height: 1.2;
                                    white-space: nowrap;">
                            {{ $status->name }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .release-progress-stepper {
        position: relative;
        padding: 0 10px;
    }
    .stepper-track {
        position: absolute;
        top: 7px;
        left: 30px;
        right: 30px;
        height: 3px;
        background: linear-gradient(
            to right, 
            #1BC5BD 0%, 
            #1BC5BD {{ $totalStatuses > 1 ? (max(0, ($currentOrder - 1)) / ($totalStatuses - 1)) * 100 : 0 }}%, 
            #6f42c1 {{ $totalStatuses > 1 ? (max(0, ($currentOrder - 1)) / ($totalStatuses - 1)) * 100 : 0 }}%, 
            #6f42c1 {{ $totalStatuses > 1 ? ($currentOrder / ($totalStatuses - 1)) * 100 : 0 }}%, 
            #E4E6EF {{ $totalStatuses > 1 ? ($currentOrder / ($totalStatuses - 1)) * 100 : 0 }}%, 
            #E4E6EF 100%
        );
        border-radius: 3px;
        z-index: 0;
    }
    .stepper-step {
        position: relative;
    }
    .active-pulse {
        animation: pulse-ring 2s ease-out infinite;
    }
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 4px rgba(140, 8, 170, 0.41); }
        50% { box-shadow: 0 0 0 8px rgba(140, 8, 170, 0.08); }
        100% { box-shadow: 0 0 0 4px rgba(140, 8, 170, 0.41); }
    }
</style>
