@extends('layouts.app')

@section('content')

	@push('css')
		<style>
			.table-responsive {
				overflow-y: auto;
				scrollbar-width: none;
				/* Firefox */
				-ms-overflow-style: none;
				/* IE 10+ */
			}

			.table-responsive::-webkit-scrollbar {
				width: 0px;
				height: 0px;
				background: transparent;
				/* Chrome/Safari/Webkit */
			}

			.table-head-custom th {
				/* position: sticky; */
				/* top: 0; */
				background-color: #F3F6F9 !important;
				/* Ensure background is opaque */
				/* z-index: 2; */
				text-align: left !important;
				border-top: none !important;
				/* box-shadow: 0 1px 1px -1px rgba(0,0,0,0.1); */
			}
		</style>
	@endpush


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
				<div class="card card-custom gutter-b shadow-sm" style="border-radius: 12px; border: 1px solid #eef0f3;">
					<div class="card-header flex-wrap border-0 pt-6 pb-0">
						<div class="card-title">
							<h3 class="card-label font-weight-bolder text-dark">{{ $title }}</h3>
						</div>
						<div class="card-toolbar">
							<!--begin::Dropdown-->
							<div class="dropdown dropdown-inline mr-2" style="display:none">
								<button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle"
									data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="svg-icon svg-icon-md">
										<!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
											width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24" />
												<path
													d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
													fill="#000000" opacity="0.3" />
												<path
													d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
													fill="#000000" />
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>Export</button>
								<!--begin::Dropdown Menu-->
								<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
									<!--begin::Navigation-->
									<ul class="navi flex-column navi-hover py-2">
										<li
											class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
											Choose an option:</li>
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
							<div class="d-flex">
								@can('Create Status')
									<!--begin::Button-->
									<a href='{{ url("$route/create") }}' class="btn btn-primary font-weight-bolder shadow-sm"
										style="border-radius: 8px;">
										<span class="svg-icon svg-icon-md">
											<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
												width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<rect x="0" y="0" width="24" height="24" />
													<circle fill="#000000" cx="9" cy="15" r="6" />
													<path
														d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
														fill="#000000" opacity="0.3" />
												</g>
											</svg>
											<!--end::Svg Icon-->
										</span>New Record</a>
									<!--end::Button-->
								@endcan

								<!--begin::Export Button-->
								@if($collection->count() > 0)
									<a href="{{ route('statuses.export') }}"
										class="btn btn-success font-weight-bolder ml-3 shadow-sm" style="border-radius: 8px;">
										<span class="svg-icon svg-icon-md">
											<!--begin::Svg Icon | path:assets/media/svg/icons/Files/Download.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
												width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<rect x="0" y="0" width="24" height="24" />
													<path
														d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z"
														fill="#000000" fill-rule="nonzero" />
													<path
														d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z"
														fill="#000000" />
												</g>
											</svg>
											<!--end::Svg Icon-->
										</span>Export Excel
									</a>
								@else
									<span class="btn btn-secondary font-weight-bolder ml-3 shadow-sm"
										style="opacity: 0.6; cursor: not-allowed; border-radius: 8px;"
										title="No data available to export">
										<span class="svg-icon svg-icon-md">
											<!--begin::Svg Icon | path:assets/media/svg/icons/Files/Download.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
												width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<rect x="0" y="0" width="24" height="24" />
													<path
														d="M7,18 L17,18 C18.1045695,18 19,18.8954305 19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 C5,18.8954305 5.8954305,18 7,18 Z M7,20 L17,20 C17.5522847,20 18,20.4477153 18,21 C18,21.5522847 17.5522847,22 17,22 L7,22 C6.44771525,22 6,21.5522847 6,21 C6,20.4477153 6.44771525,20 7,20 Z"
														fill="#000000" fill-rule="nonzero" />
													<path
														d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,13.5857864 L15.2928932,11.2928932 C15.6834175,10.9023689 16.3165825,10.9023689 16.7071068,11.2928932 C17.0976311,11.6834175 17.0976311,12.3165825 16.7071068,12.7071068 L12.7071068,16.7071068 C12.3165825,17.0976311 11.6834175,17.0976311 11.2928932,16.7071068 L7.29289322,12.7071068 C6.90236893,12.3165825 6.90236893,11.6834175 7.29289322,11.2928932 C7.68341751,10.9023689 8.31658249,10.9023689 8.70710678,11.2928932 L11,13.5857864 L11,3 C11,2.44771525 11.4477153,2 12,2 Z"
														fill="#000000" />
												</g>
											</svg>
											<!--end::Svg Icon-->
										</span>Export Excel
									</span>
								@endif
								<!--end::Export Button-->
							</div>
						</div>
					</div>
					<div class="card-body">
						<!--begin: Datatable-->

						<div class="table-responsive">
							<table class="table table-hover table-vertical-center" id="dfUsageTable" pagination="true"
								pagination-size="50">
								<thead class="table-head-custom">
									<tr class="text-uppercase text-muted"
										style="letter-spacing: 0.05rem; font-size: 0.9rem;">
										<th style="min-width: 50px;">#</th>
										<th style="min-width: 150px;">Status Name</th>
										<th style="min-width: 120px;">Stage</th>
										<th style="min-width: 120px;">Workflow Type</th>
										<th style="min-width: 150px;">Set By Group</th>
										<th style="min-width: 150px;">View By Group</th>
										@can('Active Status')
											<th style="min-width: 100px;">Active</th>
										@endcan
										<th style="min-width: 100px;">Action</th>
									</tr>
								</thead>
								<tbody>
									@include("$view.loop")
								</tbody>
							</table>
						</div>
						<!--end: Datatable-->
					</div>
				</div>
				<!--end::Card-->
			</div>
			<!--end::Container-->
		</div>
		<!--end::Entry-->
	</div>
	<!--end::Content-->


@endsection