@extends('layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex flex-column">
                    <!--begin::Title-->
                    <h2 class="text-white font-weight-bold my-2 mr-5">Top Management CRS</h2>
                    <!--end::Title-->
                </div>
                <!--end::Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!--begin::Navigation Tabs-->
                    @if($workflows_with_top_management_crs->count() > 0)
                        <!--begin: Tabs Navigation-->
                        <div class="card card-custom mb-7">
                            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                                <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-2x nav-tabs-line-primary" role="tablist">
                                    @foreach($workflows_with_top_management_crs as $index => $workflow)
                                        @php
                                            $is_active = $workflow->id === $activeTabId;
                                            $count = $top_management_crs_by_workflow[$workflow->id]->count() ?? 0;
                                        @endphp
                                        <li class="nav-item">
                                            <a class="nav-link d-flex align-items-center {{ $is_active ? 'active' : '' }}"
                                               data-toggle="tab"
                                               href="#workflow_tab_{{ $workflow->id }}"
                                               role="tab"
                                               aria-selected="{{ $is_active ? 'true' : 'false' }}">
                                                <span class="nav-icon mr-2">
                                                    <i class="flaticon2-layers-1 icon-lg"></i>
                                                </span>
                                                <span class="nav-text font-weight-bolder">{{ $workflow->name }}</span>
                                                @if($count > 0)
                                                    <span class="label label-light-{{ $is_active ? 'primary' : 'dark' }}-inline label-pill font-weight-bold ml-2">
                                                        {{ $count }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!--end: Tabs Navigation-->

                        <!--begin: Tab Content-->
                        <div class="tab-content">
                            @foreach($workflows_with_top_management_crs as $index => $workflow)
                                @php
                                    $is_active = $workflow->id === $activeTabId;
                                    $collection = $top_management_crs_by_workflow[$workflow->id] ?? collect();
                                @endphp
                                <div class="tab-pane fade {{ $is_active ? 'show active' : '' }}"
                                     id="workflow_tab_{{ $workflow->id }}"
                                     role="tabpanel"
                                     aria-labelledby="workflow_tab_{{ $workflow->id }}">

                                    <!--begin::Card-->
                                    <div class="card">
                                        <div class="card-header flex-wrap border-0 pt-6 pb-0">
                                            <div class="card-title">
                                                <h3 class="card-label">Top Management CRS - {{ $workflow->name }}</h3>
                                            </div>
                                            <div class="card-toolbar">
                                                <!--begin::Dropdown-->
                                                <div class="dropdown dropdown-inline mr-2" style="display:none">
                                                    <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="svg-icon svg-icon-md">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                                    <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.28475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3"></path>
                                                                    <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000"></path>
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>Export</button>
                                                    <!--begin::Dropdown Menu-->
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                        <!--begin::Navigation-->
                                                        <ul class="navi flex-column navi-hover py-2">
                                                            <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Choose an option:</li>
                                                            <li class="navi-item">
                                                                <a href="#" class="navi-link">
                                                                    <span class="navi-icon">
                                                                        <i class="la la-print"></i>
                                                                    </span>
                                                                    <span class="navi-text">Print</span>
                                                                </a>
                                                            </li>
                                                            <li class="navi-item">
                                                                <a href="#" class="navi-link">
                                                                    <span class="navi-icon">
                                                                        <i class="la la-copy"></i>
                                                                    </span>
                                                                    <span class="navi-text">Copy</span>
                                                                </a>
                                                            </li>
                                                            <li class="navi-item">
                                                                <a href="#" class="navi-link">
                                                                    <span class="navi-icon">
                                                                        <i class="la la-file-excel-o"></i>
                                                                    </span>
                                                                    <span class="navi-text">Excel</span>
                                                                </a>
                                                            </li>
                                                            <li class="navi-item">
                                                                <a href="#" class="navi-link">
                                                                    <span class="navi-icon">
                                                                        <i class="la la-file-text-o"></i>
                                                                    </span>
                                                                    <span class="navi-text">CSV</span>
                                                                </a>
                                                            </li>
                                                            <li class="navi-item">
                                                                <a href="#" class="navi-link">
                                                                    <span class="navi-icon">
                                                                        <i class="la la-file-pdf-o"></i>
                                                                    </span>
                                                                    <span class="navi-text">PDF</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        <!--end::Navigation-->
                                                    </div>
                                                    <!--end::Dropdown Menu-->
                                                </div>
                                                <!--end::Button-->
                                                <!--begin::Button-->
                                                <form action="{{ route('export.top_management.table') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-light-primary font-weight-bolder">
                                                        <span class="svg-icon svg-icon-md">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Files/Export.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                                    <path d="M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z" fill="#000000"></path>
                                                                    <polygon fill="#000000" opacity="0.3" points="6 8 18 8 18 10 6 10"></polygon>
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>Export Table
                                                    </button>
                                                </form>
                                                <!--end::Button-->
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!--begin::Table-->
                                            <div class="table-responsive">
                                                <table id="dfUsageTable" class="table table-checkable">
                                                    <thead>
                                                        <tr>
                                                            <th>ID#</th>
                                                            <th>Title</th>
                                                            <th>Status</th>
                                                            <th>CR Manager</th>
                                                            <th>Target System</th>
                                                            <th>CR Type</th>
                                                            <th>Top Management</th>
                                                            <th>On Behalf</th>
                                                            <th>On Hold</th>
                                                            <th>Design Duration</th>
                                                            <th>Start Design Time</th>
                                                            <th>End Design Time</th>
                                                            <th>Development Duration</th>
                                                            <th>Start Development Time</th>
                                                            <th>End Development Time</th>
                                                            <th>Test Duration</th>
                                                            <th>Start Test Time</th>
                                                            <th>End Test Time</th>
                                                            <th>CR Duration</th>
                                                            <th>Start CR Time</th>
                                                            <th>End CR Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($collection as $cr)
                                                            @php
                                                                $current_status = $cr->getCurrentStatus();
                                                                $status_name = $current_status ? $current_status->status->name : 'N/A';
                                                            @endphp
                                                            <tr>
                                                                <th scope="row" class="align-middle text-center">
                                                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                                                        <div class="d-flex flex-column align-items-center">
                                                                            @can('Edit ChangeRequest')
                                                                                <a href='{{ url("change_request") }}/{{ $cr->id }}/edit'>{{ $cr->cr_no }}</a>
                                                                            @else
                                                                                @can('Show ChangeRequest')
                                                                                    <a href='{{ url("change_request") }}/{{ $cr->id }}'>{{ $cr->cr_no }}</a>
                                                                                @else
                                                                                    {{ $cr->cr_no }}
                                                                                @endcan
                                                                            @endcan
                                                                            @if($cr->isOnGoing())
                                                                                <span class="badge badge-success mt-1"
                                                                                      style="font-size: 0.7rem; padding: 0.35rem 0.65rem; font-weight: 500; border-radius: 0.375rem; background: linear-gradient(135deg, #50cd89 0%, #47be7d 100%); box-shadow: 0 2px 4px rgba(80, 205, 137, 0.2);">
                                                                                    On Going
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </th>
                                                                <td>{{ $cr->title }}</td>
                                                                <td>{{ $status_name }}</td>
                                                                <td>{{ $cr->member?->user_name }}</td>
                                                                <td>{{ $cr->application?->name }}</td>
                                                                <td>{{ $cr->ticket_type }}</td>
                                                                <td>{{ $cr->top_management == '1' ? 'YES' : 'N/A' }}</td>
                                                                <td>{{ $cr->on_behalf_status }}</td>
                                                                <td>{{ $cr->hold == '1' ? 'YES' : 'N/A' }}</td>
                                                                <td>{{ $cr->design_duration }}</td>
                                                                <td>{{ $cr->start_design_time }}</td>
                                                                <td>{{ $cr->end_design_time }}</td>
                                                                <td>{{ $cr->develop_duration }}</td>
                                                                <td>{{ $cr->start_develop_time }}</td>
                                                                <td>{{ $cr->end_develop_time }}</td>
                                                                <td>{{ $cr->test_duration }}</td>
                                                                <td>{{ $cr->start_test_time }}</td>
                                                                <td>{{ $cr->end_test_time }}</td>
                                                                <td>{{ $cr->CR_duration }}</td>
                                                                <td>{{ $cr->start_CR_time }}</td>
                                                                <td>{{ $cr->end_CR_time }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="17" class="text-center">
                                                                    <div class="py-4">
                                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                                        <h5 class="text-muted">No Top Management Change Requests found</h5>
                                                                        <p class="text-muted">No CRs are currently marked as Top Management for {{ $workflow->name }}.</p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--end::Table-->
                                        </div>
                                    </div>
                                    <!--end::Card-->
                                </div>
                            @endforeach
                        </div>
                        <!--end: Tab Content-->
                    @else
                        <!--begin: No Data State-->
                        <div class="alert alert-light text-center" role="alert">
                            <i class="la la-inbox text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No Top Management Change Requests Found</h4>
                        </div>
                        <!--end: No Data State-->
                    @endif
                    <!--end::Navigation Tabs-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection
