@extends('layouts.app')

@section('content')

	@include('change_request.partials.styles')

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
							@if(session('status'))
								<div class="card-header">

									@php
										// to get the cr id from the session message
										preg_match('/CR#(\d+)/', session('status'), $matches);
										$cr_id = session('cr_id') ?? null;
										$cr_link = $cr_id ? route('show.cr', $cr_id) : null;
									@endphp

									<div id="success-message" style="margin-top: 20px; color: rgb(2, 8, 2); font-weight: bold;">
										{{ session('status') }}:
										@if($cr_link)
											<a href="{{ $cr_link }}" target="_blank">View CR</a>
										@endif
									</div>
								</div>
							@endif


							<!--begin::Form-->
							<form class="form" action='{{url("$route")}}' method="post" enctype="multipart/form-data">
								{{ csrf_field() }}

								<div class="card-header d-flex justify-content-between align-items-center">
									<h3 class="card-title m-0 text-info">
										{{ $title }}
									</h3>
								</div>

								<input type="hidden" name="workflow_type_id" value="{{$workflow_type_id}}">
								<div class="card-body">
									<div class="form-group row">
										@include("$view.custom_fields")
									</div>
								</div>

								<div class="card-footer" style="width: 100%;float: right;">
									<button type="submit" id="submit_button" class="btn btn-success mr-2"
										disabled>Submit</button>
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

@push('script')





	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const form = document.querySelector("form");

			form.addEventListener("submit", function (event) {

				const submitButton = form.querySelector("button[type='submit']");
				//submitButton.disabled = true;
			});
		});
	</script>

    @include('change_request.partials.on_behalf_script')

	<script>
		function viewCR(url) {

			window.location.href = url;
		}
	</script>

	<script>

		$(window).on('load', function () {
			check_division_manager_email();
		});
		const submitButton = $('#submit_button');
		const emailFeedback = $('#email_feedback');

		$("#division_manager").on('change', function () {

			check_division_manager_email();
		});

		function check_division_manager_email() {
			var workflow_type_id = {{ $workflow_type_id }};
			if (workflow_type_id == 9) {
				submitButton.prop("disabled", false);
			}
			else if (workflow_type_id == 13) {
				submitButton.prop("disabled", false);
			}
			else {
				submitButton.prop("disabled", true);
				emailFeedback.text("");
				emailFeedback.removeClass('text-success');
				const email = $("#division_manager").val();
				const divisionManagerInput = $("#division_manager");
				if (email) {

					$.ajax({
						headers: {
							'X-CSRF-TOKEN': "{{ csrf_token() }}"
						},
						url: '{{url("/")}}/check-division-manager',
						//data: JSON.stringify({ email: email }),
						//processData: false,
						data: { email: email },
						dataType: 'JSON',
						type: 'POST',
						success: function (data) {
							if (data.valid) {
								//submitButton.disabled = false;
								submitButton.prop("disabled", false);
								divisionManagerInput.removeClass('is-invalid');
								divisionManagerInput.addClass('is-valid');
								emailFeedback.text(data.message);
								emailFeedback.removeClass('text-danger');
								emailFeedback.addClass('text-success');
							}
							else {

								submitButton.prop("disabled", true);
								divisionManagerInput.removeClass('is-valid');
								divisionManagerInput.addClass('is-invalid');
								emailFeedback.text(data.message);
								emailFeedback.removeClass('text-success');
								emailFeedback.addClass('text-danger');

							}
						}
					});
				}
			}

		}


	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {

			const applicationIdField = document.querySelector('[name="application_id"]');
			const divisionManagerField = document.getElementById("division_manager");

			function handleApplicationId() {

				const selectedText =
					applicationIdField.options[applicationIdField.selectedIndex].text;

				if (selectedText === "TMS") {
					divisionManagerField.value = "{{ config('constants.tms_division_manager.0') }}";
					divisionManagerField.readOnly = true;
				} else {
					divisionManagerField.value = "";
					divisionManagerField.readOnly = false;
				}
			}

			// Run on page load
			handleApplicationId();

			// Run when application_id changes
			applicationIdField.addEventListener("change", handleApplicationId);
		});
	</script>

@endpush