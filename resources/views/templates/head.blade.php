<!DOCTYPE html>
<!--
Template Name: Metronic - Bootstrap 4 HTML, React, Angular 11 & VueJS Admin Dashboard Theme
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: https://1.envato.market/EA4JP
Renew Support: https://1.envato.market/EA4JP
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en">
<!--begin::Head-->

<head>
	<base href="../../../">
	<meta charset="utf-8" />
	<title>TMS</title>

	<meta name="description" content="Scrollable datatables examples" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<link rel="canonical" href="https://keenthemes.com/metronic" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!--begin::Fonts-->
	<link rel="stylesheet" href="{{asset('public/new_theme/font.css')}}" />
	<!--end::Fonts-->
	<link href="{{asset('public/new_theme/assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet"
		type="text/css" />
	<!--end::Fonts-->
	<!--begin::Global Theme Styles(used by all pages)-->
	<link href="{{asset('public/new_theme/assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet"
		type="text/css" />
	<link href="{{asset('public/new_theme/assets/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet"
		type="text/css" />
	<link href="{{asset('public/new_theme/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
	<link href="{{asset('public/new_theme/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
	<link src="{{asset('public/new_theme/assets/css/charts.css')}}" rel="stylesheet" type="text/css">
	</link>

	<!--end::Global Theme Styles-->
	<!--begin::Layout Themes(used by all pages)-->
	<!--end::Layout Themes-->

	<link rel="shortcut icon" href="{{asset('public/logo-icon.png')}}" />


	<!-- All Custom styles include here -->
	<link href="{{ asset('public/css/custom-style.css') }}" rel="stylesheet" type="text/css" />

	<link href="{{ asset('public/css/custom_change_request.css') }}" rel="stylesheet" type="text/css" />
	<!-- All Custom styles include here -->


	@stack('css')



</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" style="background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.jpg')}})"
	class="quick-panel-right demo-panel-right offcanvas-right header-fixed subheader-enabled page-loading">