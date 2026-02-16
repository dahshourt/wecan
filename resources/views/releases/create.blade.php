
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
								<div class="row">
								
                                    
									<div class="col-md-12">
										


										<!--begin::Card-->
										<div class="card card-custom gutter-b example example-compact">
											<div class="card-header">
												<h3 class="card-title">Add {{ $form_title }}</h3>
												
											</div>
											<!--begin::Form-->
											<form class="form" action='{{url("$route")}}' method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                @include("$view.form")
												<div class="card-footer">
													<button type="submit" class="btn btn-success mr-2">Submit</button>
													<a href="{{ route("$route.index") }}" class="btn btn-primary">cancel</a>
												</div>
											</form>
											<!--end::Form-->
										</div>
										<!--end::Card-->

                                        
									</div>
								</div>
							</div>
							<!--end::Container-->
						</div>
						<!--end::Entry-->
					</div>
					<!--end::Content-->

@endsection
