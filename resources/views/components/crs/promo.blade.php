@props([
    'isNotViewer',
    'collection',
    'userGroup'
])

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center">Promo ID</th>
            <th>Promo Subject</th>
            @if($isNotViewer)
                <th>Promo Description</th>
                <th>Status</th>
                <th>Requester</th>
                <th>Requester Department</th>
                <th>Rejection Reason</th>
                <th>Accumulative MD's</th>
                <th>Deployment Date</th>
                <th>Creation Date</th>
                <th>Last Update Date</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @if($collection)

            @foreach ($collection as $item)
                @php

                    if (session('default_group')) {
                        $default_group = session('default_group');
                    } else {
                        $default_group = auth()->user()->default_group;
                    }
                    $current_status = $item->getCurrentStatus()->status;
                    $view_technical_team_flag = $current_status->view_technical_team_flag;
                    $assigned_technical_teams = $item->technical_Cr? $item->technical_Cr->technical_cr_team->pluck('group_id')->toArray() : [];
                    $check_if_status_active = $item->technical_Cr?$item->technical_Cr->technical_cr_team->where('group_id',$default_group)->where('status','0')->count() : 0;
                @endphp

                @if(!$view_technical_team_flag || ($view_technical_team_flag && in_array($default_group, $assigned_technical_teams) && $check_if_status_active))

                    <tr class="cr-row" data-toggle-details="1" data-cr-id="{{ $item->id }}">
                        <th scope="row" class="align-middle text-center">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <button type="button" class="btn btn-clean btn-icon btn-sm mb-1 js-toggle-cr-details"
                                        data-cr-id="{{ $item->id }}" aria-expanded="false" title="Toggle row details">
                                    <i class="la la-angle-down"></i>
                                </button>

                                <div class="d-flex flex-column align-items-center">
                                    @can('Edit ChangeRequest')
                                        <a href='{{ url("$route") }}/{{ $item->id }}/edit'>{{ $item->cr_no }}</a>
                                    @else
                                        @can('Show ChangeRequest')
                                            <a href='{{ url("$route") }}/{{ $item->id }}'>{{ $item->cr_no }} </a>
                                        @else
                                            {{ $item->cr_no }}
                                        @endcan
                                    @endcan
                                </div>
                            </div>
                        </th>

                        <th scope="row">{{ $item->title }}</th>
                        @if($isNotViewer)
                            <td>
                                <span class="description-preview text-primary"
                                      data-description="{{ e(json_encode($item->description, JSON_UNESCAPED_UNICODE)) }}"
                                      role="button">
                                    {{ \Illuminate\Support\Str::limit($item->description, 50) }}
                                </span>
                            </td>
                            <td>{{ $current_status?->name }}</td>
                            <td>{{ $item->requester?->user_name }}</td>
                            <td>{{ $item->department?->name }}</td>
                            <td>{{ $item->rejectionReason?->name }}</td>
                            <td>{{ $item->accumulativeMDs?->custom_field_value }}</td>
                            <td>{{ $item->deploymentDate?->custom_field_value }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->updated_at }}</td>
                            <td></td>
                        @endif
                    </tr>
                @endif


                @php
                    $detailsColspan = 12;
                    $statuses = $item->getallCurrentStatus();
                @endphp
                <tr class="cr-details-row" data-cr-id="{{ $item->id }}" style="display:none;">
                    <td colspan="{{ $detailsColspan }}" class="p-0">
                        <div style="background: #f8f9fb; padding: 1.25rem 1rem; border-top: 2px solid #e4e6ef;">

                            <table class="table table-hover mb-0"
                                   style="background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                <thead style="background: linear-gradient(to right, #f5f8fa, #e9ecef);">
                                <tr>
                                    <th class="font-weight-bold text-uppercase"
                                        style="font-size: 0.75rem; color: #5e6278; letter-spacing: 0.5px; padding: 1rem 1.25rem; border: none;">
                                        <i class="la la-users mr-1"></i> Group
                                    </th>
                                    <th class="font-weight-bold text-uppercase"
                                        style="font-size: 0.75rem; color: #5e6278; letter-spacing: 0.5px; padding: 1rem 1.25rem; border: none;">
                                        <i class="la la-check-circle mr-1"></i> Status
                                    </th>
                                    <th class="font-weight-bold text-uppercase text-center"
                                        style="font-size: 0.75rem; color: #5e6278; letter-spacing: 0.5px; padding: 1rem 1.25rem; border: none;">
                                        <i class="la la-cog mr-1"></i> Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($statuses as $status)
                                    <tr style="border-bottom: 1px solid #f3f4f6; transition: all 0.2s;">
                                        <td class="align-middle" style="padding: 1rem 1.25rem;">
                                <span class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                    {{ $status->currentGroup->name ?? 'N/A' }}
                                </span>
                                        </td>
                                        <td class="align-middle" style="padding: 1rem 1.25rem;">
                                <span class="badge badge-primary"
                                      style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 500; border-radius: 4px;">
                                    ( {{ $status->status->status_name ?? 'N/A' }} )
                                    @if($status->reference_group_id)
                                        tech team ({{ $status->referenceGroup->name ?? 'N/A' }})
                                    @elseif($status->reference_group_id)
                                        by ({{ $status->previousGroup->name ?? 'N/A' }})
                                    @endif
                                </span>
                                        </td>
                                        <td class="align-middle text-center" style="padding: 1rem 1.25rem;">
                                            <div class="btn-group btn-group-sm" role="group">
                                                @can('Show ChangeRequest')
                                                    <a href='{{ url("$route") }}/{{ $item->id }}'
                                                       class="btn btn-light-primary btn-sm"
                                                       title="View"
                                                       style="padding: 0.4rem 0.9rem; border-radius: 4px 0 0 4px;">
                                                        <i class="la la-eye"></i> View
                                                    </a>
                                                @endcan


                                                @can('Edit ChangeRequest')
                                                    @if(in_array($status->new_status_id,$userGroup->group_statuses->where('type', 2)->pluck('status_id')->toArray()))
                                                        @if(!$status->group_id OR $status->current_group_id == $userGroup->id )
                                                            @if($item->getSetStatus()->count() > 0)
                                                                <a href='{{ url("$route") }}/{{ $item->id }}/edit?reference_status={{ $status->id }}'
                                                                   class="btn btn-light-success btn-sm"
                                                                   title="Edit"
                                                                   style="padding: 0.4rem 0.9rem; border-radius: 0 4px 4px 0;">
                                                                    <i class="la la-edit"></i> Edit
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4" style="background: #fafbfc;">
                                            <i class="la la-info-circle text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mb-0 mt-2">No status records found</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>

            @endforeach
        @else
            <tr>
                <td colspan="7" style="text-align:center">No Data Found</td>
            </tr>
        @endif
        </tbody>
    </table>
    <!--end: Datatable-->
</div>

<!-- Description Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descriptionModalLabel">Full Description</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="white-space: pre-wrap;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
$(document).on('click', '.description-preview', function (event) {
    event.preventDefault();
    let fullDescription = $(this).attr('data-description') || '';
    try {
        // Decode HTML entities and parse JSON
        fullDescription = $('<div>').html(fullDescription).text();
        fullDescription = JSON.parse(fullDescription);
    } catch (e) {
        // If parsing fails, use the raw value
        console.warn('Failed to parse description JSON:', e);
    }
    $('#descriptionModal .modal-body').text(fullDescription);
    $('#descriptionModal').modal('show');
});
</script>
@endpush

@push('css')
<style>
.description-preview {
    cursor: pointer;
    transition: color 0.2s;
}

.description-preview:hover {
    color: #0056b3 !important;
}
</style>
@endpush
