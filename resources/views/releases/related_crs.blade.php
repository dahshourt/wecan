@php
    $isView = request()->routeIs('*.show');
    $linkedCrs = isset($changeRequests) ? $changeRequests : ($row->changeRequests ?? collect());
    $hasLinkedCrs = $linkedCrs->isNotEmpty();
@endphp
    <!-- Section: Linked Change Requests -->
    @if(isset($row))
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bolder">Related Change Requests</h3>
            <a href="{{ $hasLinkedCrs ? route('kpis.export-crs', ['kpi' => $row->id]) : '#' }}"
               class="btn btn-success font-weight-bolder btn-sm {{ $hasLinkedCrs ? '' : 'disabled' }}"
               @unless($hasLinkedCrs) aria-disabled="true" tabindex="-1" @endunless>
                <span class="svg-icon svg-icon-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <path d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z" fill="#000000" fill-rule="nonzero"/>
                            <path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" fill="#000000"/>
                        </g>
                    </svg>
                </span>Export Excel
            </a>
        </div>
        <div class="card-body">
            @if(!$isView)
            <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
                <div class="alert-icon"><i class="flaticon2-search-1"></i></div>
                <div class="alert-text">
                    <div class="input-group">
                        <input type="text" id="kpi_cr_no" class="form-control" placeholder="Enter CR number to link...">
                        <div class="input-group-append">
                            <button type="button" id="kpi_cr_search_btn" class="btn btn-primary font-weight-bold">Search & Link</button>
                        </div>
                    </div>
                    <small id="kpi_cr_search_message" class="form-text text-danger mt-2 font-weight-bold"></small>
                </div>
            </div>

            <div id="kpi_cr_search_result" class="card card-custom bg-light-success gutter-b" style="display:none;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <span class="font-weight-bolder mr-2">Found:</span>
                        <span id="kpi_cr_result_no" class="font-weight-bold mr-3"></span>
                        <span id="kpi_cr_result_title" class="mr-3"></span>
                        <span class="label label-inline label-white mr-3" id="kpi_cr_result_status"></span>
                    </div>
                    <div>
                        <a href="#" target="_blank" id="kpi_cr_result_link" class="btn btn-sm btn-light-primary font-weight-bold mr-2">View CR</a>
                        <button type="button" id="kpi_cr_attach_btn" class="btn btn-sm btn-success font-weight-bold">Link to KPI</button>
                    </div>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center" id="kt_advance_table_widget_1">
                    <thead>
                        <tr class="text-left">
                            <th class="pl-0" style="width: 100px">CR #</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Workflow</th>
                            @if(!$isView)
                                <th class="text-right pr-0">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="kpi_cr_table_body">
                        @forelse($linkedCrs as $cr)
                            <tr data-cr-id="{{ $cr->id }}">
                                <td class="pl-0 font-weight-bolder">{{ $cr->cr_no }}</td>
                                <td>
                                    <a href="{{ route('show.cr', $cr->id) }}" target="_blank" class="text-dark-75 text-hover-primary font-weight-bold">{{ $cr->title }}</a>
                                </td>
                                <td>
                                    <span class="label label-lg label-light-info label-inline font-weight-bold">{{ optional(optional($cr->CurrentRequestStatuses)->status)->status_name ?? '-' }}</span>
                                </td>
                                <td>{{ $cr->workflowType->name ?? '-' }}</td>
                                @if(!$isView)
                                <td class="text-right pr-0">
                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm js-detach-cr" data-cr-id="{{ $cr->id }}" title="Remove">
                                        <i class="flaticon2-trash"></i>
                                    </button>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr class="no-records">
                                <td colspan="{{ $isView ? 4 : 5 }}" class="text-center text-muted font-weight-bold py-5">No Change Requests linked to this KPI.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
