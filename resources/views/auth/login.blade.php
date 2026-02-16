<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
	<base href="../../../../">
	<meta charset="utf-8" />
	<title>TMS | Login</title>
	<meta name="description" content="Login page" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<!--begin::Fonts-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Page Custom Styles-->
	<link href="{{asset('public/new_theme/assets/css/pages/login/login-custom.css')}}" rel="stylesheet"
		type="text/css" />
	<!--end::Page Custom Styles-->
	<!--begin::Global Theme Styles-->
	<link href="{{asset('public/new_theme/assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet"
		type="text/css" />
	<link href="{{asset('public/new_theme/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
	<!--end::Global Theme Styles-->
	<link rel="shortcut icon" href="{{asset('public/assets/images/logo-icon.png')}}" />
</head>
<!--end::Head-->
<!--begin::Body-->

<body>

	<div class="login-page-container">
		<!-- LEFT PANEL -->
		<div class="login-left-panel">
			<!-- Logo -->
			<div class="brand-logo">
				<!-- Using logo-we2.png assuming it's the transparent white version or similar -->
				<!-- If not, we might need to use text or finding the correct asset -->
				<img src="{{asset('public/logo-we2.png')}}" alt="Telecom Egypt" style="max-width: 100%; height: auto;">
			</div>

			<div class="brand-hero-text">
				<h1>Empower your<br>enterprise change<br>management.</h1>
				<!-- <p>Experience the next generation of operational agility with AI-driven insights and streamlined
					approval workflows.</p> -->
			</div>
		</div>

		<!-- RIGHT PANEL -->
		<div class="login-right-panel">
			<div class="login-form-wrapper">
				<div class="mb-5 text-center text-lg-left">
					<img src="{{asset('public/TMS.svg')}}" alt="TMS" class="mb-10" style="height: 60px;">
					<h2 class="login-title">Sign In</h2>
					<p class="login-subtitle">Enter your details to login to your account</p>
				</div>

				<form class="form" method="POST" action="{{ route('login.custom') }}">
					@csrf

					<!-- Error Alerts -->
					@if($errors->any())
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<strong>{{ $errors->first() }}</strong>
						</div>
					@endif
					@if ($message = Session::get('failed'))
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<strong>{{ $message }}</strong>
						</div>
					@endif

					<div class="form-group mb-5">
						<label>User Name <span class="text-danger">*</span></label>
						<input class="form-control custom-input" type="text" placeholder="User Name" name="user_name"
							autocomplete="off" required />
					</div>

					<div class="form-group mb-5">
						<label>Password <span class="text-danger">*</span></label>
						<div class="input-icon input-icon-right">
							<input class="form-control custom-input" type="password" placeholder="Password"
								name="password" required />
							<span><i class="flaticon-eye text-muted"></i></span>
						</div>
						<div class="d-flex justify-content-end mt-2">
							<a href="#" class="forgot-password-link">Forget Password</a>
						</div>
					</div>

					<!-- Hidden Remember Me or Visible if needed. Design didn't show it explicitly but good to keep functionally -->
					<div class="form-group d-none">
						<label class="checkbox">
							<input type="checkbox" name="remember" /> Remember me
							<span></span>
						</label>
					</div>

					<div class="form-group mt-10">
						<button type="submit" class="btn btn-login btn-block">Login</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>var HOST_URL = "{{ url('/') }}";</script>
	<!--begin::Global Theme Bundle-->
	<script src="{{asset('public/new_theme/assets/plugins/global/plugins.bundle.js')}}"></script>
	<script src="{{asset('public/new_theme/assets/js/scripts.bundle.js')}}"></script>
	<!--end::Global Theme Bundle-->
</body>
<!--end::Body-->

</html>