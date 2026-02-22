@extends('layouts.app')

@section('content')

@php
	$user_group = session()->has('current_group') ? session('current_group') : auth()->user()->defualt_group->id;
	$user_group =\App\Models\Group::find($user_group);

    $user_roles_names = auth()->user()->roles->pluck('name');
    $current_user_is_just_a_viewer = count($user_roles_names) === 1 && $user_roles_names[0] === 'Viewer';
@endphp


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
										<h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
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

								<!--begin::Card-->
								<div class="card">
									<div class="card-header flex-wrap border-0 pt-6 pb-0">
										<div class="card-title">
											<h3 class="card-label">{{ $title }}
										</div>
										<div class="card-toolbar">
                                            @if(isset($searchType) && $searchType === 'advanced')
                                                <form action="{{ route('advanced.search.export', request()->query()) }}" method="POST"
                                                    style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-light-primary font-weight-bolder">
                                                        <span class="svg-icon svg-icon-md">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                                viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z"
                                                                        fill="#000000" />
                                                                    <polygon fill="#000000" opacity="0.3" points="6 8 18 8 18 10 6 10" />
                                                                </g>
                                                            </svg>
                                                        </span>
                                                        Export Table
                                                    </button>
                                                </form>
                                            @else
											<!--begin::Dropdown-->
											<div class="dropdown dropdown-inline mr-2" style="display:none">
												<button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<span class="svg-icon svg-icon-md">
													<!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
													<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
														<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
															<rect x="0" y="0" width="24" height="24" />
															<path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
															<path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
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
											<!--end::Dropdown-->
                                            @endif
											<!--begin::Button-->

											<!--end::Button-->
										</div>
									</div>
									<div class="card-body">
										<!--begin: Datatable-->
										<div class="table-responsive">
										<table class="table table-bordered" >
											<thead>
												<tr>
                                                    @if(isset($searchType) && $searchType === 'advanced')
                                                        <th>CR ID</th>
                                                        <th>Title</th>
                                                        <th>Category</th>
                                                        <th>Release</th>
                                                        <th>Current Status</th>
                                                        <th>Requester</th>
                                                        <th>Requester Email</th>
                                                        <th>Design Duration</th>
                                                        <th>Dev Duration</th>
                                                        <th>Test Duration</th>
                                                        <th>Creation Date</th>
                                                        <th>Requesting Department</th>
                                                        <th>Targeted System</th>
                                                        
                                                        <th>Last Action Date</th>
                                                        <th>Action</th>
                                                    @else
                                                        @php
                                                            $firstItem = $items->first();
                                                        @endphp
                                                        @if($firstItem && $firstItem->workflow_type_id == 5)
                                                            <th>#</th>
                                                            <th>Sbject</th>
                                                            <th>Sumarry</th>
                                                            <th>status</th>
                                                            <th>App</th>
                                                            <th>Release Name</th>
                                                            <th>Go Live Planned Date</th>
                                                            <th> planned_start_iot_date</th>
                                                            <th> planned_end_iot_date</th>
                                                            <th> planned_start_e2e_date</th>
                                                            <th> planned_end_e2e_date</th>
                                                            <th> planned_start_uat_date</th>
                                                            <th> planned_end_uat_date</th>
                                                            <th> planned_start_smoke_test_date</th>
                                                            <th> planned_end_smoke_test_date</th>
                                                            
                                                            {{--<th>#</th>--}}
                                                        @else
                                                            <th>#</th>
                                                            <th>Sbject</th>
                                                            <th>Sumarry</th>
                                                            <th>status</th>
                                                            <th>App</th>
                                                            <th>Design Duration</th>
                                                            <th>Start Design Time</th>
                                                            <th>End Design Time</th>
                                                            <th>Development Duration</th>
                                                            <th>Start Development Time</th>
                                                            <th>End Development Time</th>
                                                            <th>Test Duration</th>
                                                            <th>Start Test Time</th>
                                                            <th>End Test Time</th>
                                                            <th>Creation Date</th>
                                                            <th>Delivery/Updated Date</th>
                                                            
                                                            {{--<th>#</th>--}}
                                                        @endif
                                                    @endif
												</tr>
											</thead>
											<tbody>
                                            @forelse($items as $cr)
											    @include("$view.loop")
                                            @empty
                                                <tr>
                                                    <td colspan="15" class="text-center">No results found</td>
                                                </tr>
                                            @endforelse
											</tbody>
										</table>
										</div>
                                        @if(isset($items) && $items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                            <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded shadow-sm">
                                                <p class="mb-0 text-primary fw-bold">Total Results: {{ $items->total() }}</p>
                                                <div>{{ $items->links() }}</div>
                                            </div>
                                        @endif
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

								<!--end::Card-->
							</div>
							                            <!--end::Container-->
                        <!--end::Entry-->
					</div>
					<!--end::Content-->


@endsection
@push('script')
<script>
$(document).on('click', '.js-toggle-cr-details', function(e) {
  e.preventDefault();
  var $btn = $(this);
  var id = $btn.data('cr-id');
  var $row = $btn.closest('tr');
  var $details = $('tr.cr-details-row[data-cr-id="' + id + '"]');
  var expanded = $btn.attr('aria-expanded') === 'true';

  if (expanded) {
    $btn.attr('aria-expanded', 'false');
    $btn.find('i.la').removeClass('la-angle-up').addClass('la-angle-down');
    $details.hide();
  } else {
    $btn.attr('aria-expanded', 'true');
    $btn.find('i.la').removeClass('la-angle-down').addClass('la-angle-up');
    if ($details.prev()[0] !== $row[0]) {
      $details.insertAfter($row);
    }
    $details.show();
}});

$(document).on('click', 'tr.cr-row', function(e) {
  if ($(e.target).closest('a, button, .js-toggle-cr-details, .dropdown-menu, .select2-container').length) {
    return;
  }
  $(this).find('.js-toggle-cr-details').trigger('click');
});

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

$(function() {
  $('tr.cr-row:first').find('.js-toggle-cr-details').trigger('click');
});
</script>
@endpush
