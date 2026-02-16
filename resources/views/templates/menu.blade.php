<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
            <!--begin::Header-->
            <div id="kt_header" class="header header-fixed">
                <div class="header-logo">
                    <a href="{{ url('/') }}">
                        <img alt="Logo" src="{{asset('public/logo-we2.png')}}" class="logo-default max-h-40px" />
                        <img alt="Logo" src="{{asset('public/logo-we.jpg')}}" class="logo-sticky max-h-40px" />
                    </a>
                </div>
                <!--begin::Container-->
                <div class="container d-flex align-items-stretch justify-content-between">
                    <!--begin::Left-->
                    <div class="d-flex align-items-stretch mr-3">
                        <!--begin::Header Logo-->

                        <!--end::Header Logo-->
                        <!--begin::Header Menu Wrapper-->
                        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                            <!--begin::Header Menu-->
                            <div id="kt_header_menu"
                                class="header-menu header-menu-left header-menu-mobile header-menu-layout-default">
                                <!--begin::Header Nav-->
                                <ul class="menu-nav">
                                    @can('Dashboard')
                                        <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                            aria-haspopup="true">
                                            <a href="{{ url('/dashboard') }}" class="menu-link">
                                                <span class="menu-text">Dashboard</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('Access Top Management CRS')
                                        <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                            aria-haspopup="true">
                                            <a href="{{ url('top_management_crs') }}" class="menu-link">
                                                <span class="menu-text">Top Management CRS</span>
                                            </a>
                                        </li>
                                    @endcan

                                    @canany(['List KPIs', 'List Projects', 'List KPI Types', 'List KPI Pillars', 'List KPI Initiatives', 'List KPI Sub-Initiatives'])
                                        <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                            aria-haspopup="true">
                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                <span class="menu-text">KPIs</span>
                                                <span class="menu-desc"></span>
                                                <i class="la la-angle-down ml-2"></i>
                                            </a>
                                            <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                                <ul class="menu-subnav">
                                                    @can('List KPIs')
                                                        <li class="menu-item" aria-haspopup="true">
                                                            <a href="{{ url('kpis') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path
                                                                                d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                fill="#000000" opacity="0.3" />
                                                                            <path
                                                                                d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                fill="#000000" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">KPIs</span>
                                                            </a>
                                                        </li>
                                                    @endcan

                                                    @can('List Projects')
                                                        <li class="menu-item" aria-haspopup="true">
                                                            <a href="{{ route('projects.index') }}" class="menu-link">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path
                                                                                d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                fill="#000000" opacity="0.3" />
                                                                            <path
                                                                                d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                fill="#000000" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">Project Manager KPI</span>
                                                            </a>
                                                        </li>
                                                    @endcan

                                                    {{-- KPI Configurations Submenu --}}
                                                    @canany(['List KPI Types', 'List KPI Pillars', 'List KPI Initiatives', 'List KPI Sub-Initiatives'])
                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover"
                                                            aria-haspopup="true">
                                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                                <span class="svg-icon menu-icon">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path
                                                                                d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z"
                                                                                fill="#000000" />
                                                                            <rect fill="#000000" opacity="0.3"
                                                                                transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519)"
                                                                                x="16.3255682" y="2.94551858" width="3"
                                                                                height="18" rx="1" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                <span class="menu-text">KPIs Configurations</span>
                                                                <i class="la la-angle-down ml-2"></i>
                                                            </a>
                                                            <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                                <ul class="menu-subnav">
                                                                    @can('List KPI Types')
                                                                        <li class="menu-item" aria-haspopup="true">
                                                                            <a href="{{ route('kpi-types.index') }}"
                                                                                class="menu-link">
                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                    <span></span>
                                                                                </i>
                                                                                <span class="menu-text">Type</span>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('List KPI Pillars')
                                                                        <li class="menu-item" aria-haspopup="true">
                                                                            <a href="{{ route('kpi-pillars.index') }}"
                                                                                class="menu-link">
                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                    <span></span>
                                                                                </i>
                                                                                <span class="menu-text">Pillars</span>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('List KPI Initiatives')
                                                                        <li class="menu-item" aria-haspopup="true">
                                                                            <a href="{{ route('kpi-initiatives.index') }}"
                                                                                class="menu-link">
                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                    <span></span>
                                                                                </i>
                                                                                <span class="menu-text">Initiative</span>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('List KPI Sub-Initiatives')
                                                                        <li class="menu-item" aria-haspopup="true">
                                                                            <a href="{{ route('kpi-sub-initiatives.index') }}"
                                                                                class="menu-link">
                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                    <span></span>
                                                                                </i>
                                                                                <span class="menu-text">Sub-Initiative</span>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    @endcanany
                                                </ul>
                                            </div>
                                        </li>
                                    @endcanany

                                    <!--check if the user have any of the below permissions -->
                                    @canany(['List Users', 'Access CustomFields', 'List Statuses', 'List Workflows', 'List Division', 'List Groups', 'List Roles', 'List Permissions', 'List Stages', 'List Parents', 'List HighLevelStatuses', 'List RejectionReasons', 'List Applications', 'List Hold Reasons'])
                                    <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                        aria-haspopup="true">
                                        <a href="javascript:;" class="menu-link menu-toggle">
                                            <span class="menu-text">Settings</span>
                                            <span class="menu-desc"></span>
                                            <i class="la la-angle-down ml-2"></i>
                                        </a>
                                        <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                            <ul class="menu-subnav">
                                                @can('List Users')
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="{{ url('users') }}" class="menu-link">
                                                            <span class="svg-icon menu-icon">
                                                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24" height="24" />
                                                                        <path
                                                                            d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                                <!--end::Svg Icon-->
                                                            </span>
                                                            <span class="menu-text">Users</span>
                                                        </a>
                                                    </li>
                                                @endcan

                                                @canany(['Access CustomFields', 'List Custom Fields'])
                                                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover"
                                                                            aria-haspopup="true">
                                                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                                                <span class="svg-icon menu-icon">
                                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Address-card.svg-->
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                                            fill-rule="evenodd">
                                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                                            <path
                                                                                                d="M6,2 L18,2 C19.6568542,2 21,3.34314575 21,5 L21,19 C21,20.6568542 19.6568542,22 18,22 L6,22 C4.34314575,22 3,20.6568542 3,19 L3,5 C3,3.34314575 4.34314575,2 6,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                                                                                fill="#000000" />
                                                                                        </g>
                                                                                    </svg>
                                                                                    <!--end::Svg Icon-->
                                                                                </span>
                                                                                <span class="menu-text">Custom Fields</span>
                                                                                <i class="la la-angle-down ml-2"></i>
                                                                            </a>
                                                                            <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                                                <ul class="menu-subnav">
                                                                                    @can('List Custom Fields')
                                                                                        <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                            data-menu-toggle="click" aria-haspopup="true">
                                                                                            <a href="{{ route('custom-fields.index') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Add Or Update Custom
                                                                                                    Fields</span>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan

                                                                                    @can('Access CustomFields')
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.create') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Create CR CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.search') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Search CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.special') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Create CR special CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.view') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">View CR CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.special.view') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">View CR Special CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.viewupdate') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Update CR CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.special.viewupdate') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Update CR Special CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.special.viewsearch') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Search Special CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.createCF') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Search Result CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.special.viewresult') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Search Result Special
                                                                                                    CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.special.viewadvanced') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Advanced Search CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                        <li class="menu-item" aria-haspopup="true">
                                                                                            <a href="{{ route('custom.fields.viewCF') }}"
                                                                                                class="menu-link">
                                                                                                <i class="menu-bullet menu-bullet-dot">
                                                                                                    <span></span>
                                                                                                </i>
                                                                                                <span class="menu-text">Update CR CF</span>
                                                                                            </a>
                                                                                        </li>
                                                                                    @endcan
                                                                                </ul>
                                                                            </div>
                                                                        </li>
                                                                        @endcan
                                                                        @can('List Statuses')
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="click" aria-haspopup="true">
                                                                                <a href="{{ url('statuses') }}" class="menu-link">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Shopping/Gift.svg-->
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                                <path
                                                                                                    d="M4,6 L20,6 C20.5522847,6 21,6.44771525 21,7 L21,8 C21,8.55228475 20.5522847,9 20,9 L4,9 C3.44771525,9 3,8.55228475 3,8 L3,7 C3,6.44771525 3.44771525,6 4,6 Z M5,11 L10,11 C10.5522847,11 11,11.4477153 11,12 L11,19 C11,19.5522847 10.5522847,20 10,20 L5,20 C4.44771525,20 4,19.5522847 4,19 L4,12 C4,11.4477153 4.44771525,11 5,11 Z M14,11 L19,11 C19.5522847,11 20,11.4477153 20,12 L20,19 C20,19.5522847 19.5522847,20 19,20 L14,20 C13.4477153,20 13,19.5522847 13,19 L13,12 C13,11.4477153 13.4477153,11 14,11 Z"
                                                                                                    fill="#000000" />
                                                                                                <path
                                                                                                    d="M14.4452998,2.16794971 C14.9048285,1.86159725 15.5256978,1.98577112 15.8320503,2.4452998 C16.1384028,2.90482849 16.0142289,3.52569784 15.5547002,3.83205029 L12,6.20185043 L8.4452998,3.83205029 C7.98577112,3.52569784 7.86159725,2.90482849 8.16794971,2.4452998 C8.47430216,1.98577112 9.09517151,1.86159725 9.5547002,2.16794971 L12,3.79814957 L14.4452998,2.16794971 Z"
                                                                                                    fill="#000000" fill-rule="nonzero"
                                                                                                    opacity="0.3" />
                                                                                            </g>
                                                                                        </svg>
                                                                                        <!--end::Svg Icon-->
                                                                                    </span>
                                                                                    <span class="menu-text">Status</span>
                                                                                </a>
                                                                            </li>
                                                                        @endcan

                                                                        @can('List Workflows')
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="click" aria-haspopup="true">
                                                                                <a href="{{ url('NewWorkFlowController') }}" class="menu-link">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                                <path
                                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                                    fill="#000000" opacity="0.3" />
                                                                                                <path
                                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                                    fill="#000000" />
                                                                                            </g>
                                                                                        </svg>
                                                                                        <!--end::Svg Icon-->
                                                                                    </span>
                                                                                    <span class="menu-text">Workflows</span>
                                                                                </a>
                                                                            </li>
                                                                        @endcan

                                                                        @canany(['List Director', 'List Division', 'List Units'])
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="hover" aria-haspopup="true">
                                                                                <a href="javascript:;" class="menu-link menu-toggle">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <img src="{{ asset('public/new_theme/assets/media/group.png') }}"
                                                                                            alt="Management" width="24" height="24" />
                                                                                    </span>
                                                                                    <span class="menu-text">Management</span>
                                                                                    <i class="la la-angle-down ml-2"></i>
                                                                                </a>
                                                                                <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                                                    <ul class="menu-subnav">
                                                                                        @can('List Division')
                                                                                            <li class="menu-item" aria-haspopup="true">
                                                                                                <a href="{{ url('division_manager') }}"
                                                                                                    class="menu-link">
                                                                                                    <i
                                                                                                        class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                                                    <span class="menu-text">Division Manager</span>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                        @can('List Director')
                                                                                            <li class="menu-item" aria-haspopup="true">
                                                                                                <a href="{{ route('directors.index') }}"
                                                                                                    class="menu-link">
                                                                                                    <i
                                                                                                        class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                                                    <span class="menu-text">Directors</span>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                        @can('List Units')
                                                                                            <li class="menu-item" aria-haspopup="true">
                                                                                                <a href="{{ route('units.index') }}" class="menu-link">
                                                                                                    <i
                                                                                                        class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                                                    <span class="menu-text">Units</span>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    </ul>
                                                                                </div>
                                                                            </li>
                                                                        @endcanany
                                                                        @canany(['List Notification Templates', 'List Notification Rules'])
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="hover" aria-haspopup="true">
                                                                                <a href="javascript:;" class="menu-link menu-toggle">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <img src="{{ asset('public/new_theme/assets/media/notification.png') }}"
                                                                                            alt="Management" width="24" height="24" />
                                                                                    </span>
                                                                                    <span class="menu-text">Notifications</span>
                                                                                    <i class="la la-angle-down ml-2"></i>
                                                                                </a>
                                                                                <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                                                    <ul class="menu-subnav">
                                                                                        @can('List Notification Templates')
                                                                                            <li class="menu-item" aria-haspopup="true">
                                                                                                <a href="{{ route('notification_templates.index') }}"
                                                                                                    class="menu-link">
                                                                                                    <i
                                                                                                        class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                                                    <span class="menu-text">Notification
                                                                                                        Templates</span>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                        @can('List Notification Rules')
                                                                                            <li class="menu-item" aria-haspopup="true">
                                                                                                <a href="{{ route('notification_rules.index') }}"
                                                                                                    class="menu-link">
                                                                                                    <i
                                                                                                        class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                                                    <span class="menu-text">Notification Rules</span>
                                                                                                </a>
                                                                                            </li>
                                                                                        @endcan
                                                                                    </ul>
                                                                                </div>
                                                                            </li>
                                                                        @endcanany

                                                                        @can('List Groups')
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="click" aria-haspopup="true">
                                                                                <a href="{{ url('groups') }}" class="menu-link">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                                <path
                                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                                    fill="#000000" opacity="0.3" />
                                                                                                <path
                                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                                    fill="#000000" />
                                                                                            </g>
                                                                                        </svg>
                                                                                        <!--end::Svg Icon-->
                                                                                    </span>
                                                                                    <span class="menu-text">Groups</span>
                                                                                </a>
                                                                            </li>
                                                                        @endcan

                                                                        @can('List Applications')
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="click" aria-haspopup="true">
                                                                                <a href="{{ url('applications') }}" class="menu-link">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                                <path
                                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                                    fill="#000000" opacity="0.3" />
                                                                                                <path
                                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                                    fill="#000000" />
                                                                                            </g>
                                                                                        </svg>
                                                                                        <!--end::Svg Icon-->
                                                                                    </span>
                                                                                    <span class="menu-text">Applications</span>
                                                                                </a>
                                                                            </li>
                                                                        @endcan

                                                                        @can('List Roles')
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="click" aria-haspopup="true">
                                                                                <a href="{{ url('roles') }}" class="menu-link">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                                <path
                                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                                    fill="#000000" opacity="0.3" />
                                                                                                <path
                                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                                    fill="#000000" />
                                                                                            </g>
                                                                                        </svg>
                                                                                        <!--end::Svg Icon-->
                                                                                    </span>
                                                                                    <span class="menu-text">Roles</span>
                                                                                </a>
                                                                            </li>
                                                                        @endcan

                                                                        @hasrole('Super Admin')
                                                                        @can('List Permissions')
                                                                            <li class="menu-item menu-item-submenu menu-item-rel"
                                                                                data-menu-toggle="click" aria-haspopup="true">
                                                                                <a href="{{ url('permissions') }}" class="menu-link">
                                                                                    <span class="svg-icon menu-icon">
                                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                                <path
                                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                                    fill="#000000" opacity="0.3" />
                                                                                                <path
                                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                                    fill="#000000" />
                                                                                            </g>
                                                                                        </svg>
                                                                                        <!--end::Svg Icon-->
                                                                                    </span>
                                                                                    <span class="menu-text">Permissions</span>
                                                                                </a>
                                                                            </li>
                                                                        @endcan
                                                                        @endhasrole
                                                            </li>
                                                            @can('List Stages')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ url('stages') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">Stages</span>
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('List Requester Departments')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ url('requester-department') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">Requester Department</span>
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('List Parents')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ url('parents') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">Parent CR</span>
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('List Hold Reasons')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ route('hold-reasons.index') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">Hold Reason</span>
                                                                    </a>
                                                                </li>
                                                            @endcan


                                                            @can('List HighLevelStatuses')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ url('high_level_status') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">High Level Statuses</span>
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('List RejectionReasons')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ url('rejection_reasons') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">Rejection Reasons</span>
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('SLA Calculations')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ url('sla-calculations') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">SLA Calculations</span>
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('Configurations')
                                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                    aria-haspopup="true">
                                                                    <a href="{{ url('configurations') }}" class="menu-link">
                                                                        <span class="svg-icon menu-icon">
                                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                                    <path
                                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                        fill="#000000" opacity="0.3" />
                                                                                    <path
                                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                        fill="#000000" />
                                                                                </g>
                                                                            </svg>
                                                                            <!--end::Svg Icon-->
                                                                        </span>
                                                                        <span class="menu-text">Configurations</span>
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                    </li>
                                                @endcanany
                            @canany(['Final Confirmation', 'Edit Testable Form', 'Edit Top Management Form', 'Admin Add Attachments and Feedback', 'shifting CRS', 'List Cab Users'])
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">Admin</span>
                                    <span class="menu-desc"></span>
                                    <i class="la la-angle-down ml-2"></i>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @can('Edit Testable Form')
                                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                aria-haspopup="true">
                                                <a href="{{ url('testable_form') }}" class="menu-link">
                                                    <span class="svg-icon menu-icon">
                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24" />
                                                                <path
                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                    fill="#000000" opacity="0.3" />
                                                                <path
                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                    fill="#000000" />
                                                            </g>
                                                        </svg>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-text">Testable Form</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('Edit Top Management Form')
                                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                aria-haspopup="true">
                                                <a href="{{ url('top_management_crs/form') }}" class="menu-link">
                                                    <span class="svg-icon menu-icon">
                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24" />
                                                                <path
                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                    fill="#000000" opacity="0.3" />
                                                                <path
                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                    fill="#000000" />
                                                            </g>
                                                        </svg>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-text">Top Managers CRS Form</span>
                                                </a>
                                            </li>
                                        @endcan



                                        @canany(['Create Top Management CRS', 'List Top Management CRS', 'Delete Top Management CRS'])
                                                        <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                            aria-haspopup="true">
                                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                                <span class="svg-icon menu-icon">
                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/General/Settings.svg-->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path
                                                                                d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                fill="#000000" opacity="0.3" />
                                                                            <path
                                                                                d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                fill="#000000" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <span class="menu-text">Top Management Admin</span>
                                                                <i class="la la-angle-down ml-2"></i>
                                                            </a>
                                                            <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                                                <ul class="menu-subnav">
                                                                    @can('List Top Management CRS')
                                                                        <li class="menu-item" aria-haspopup="true">
                                                                            <a href="{{ route('top_management_crs.list') }}"
                                                                                class="menu-link">
                                                                                <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                                <span class="menu-text">List Top Management CRS</span>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                    @can('Create Top Management CRS')
                                                                        <li class="menu-item" aria-haspopup="true">
                                                                            <a href="{{ route('top_management_crs.create') }}"
                                                                                class="menu-link">
                                                                                <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                                <span class="menu-text">Create Top Management CRS</span>
                                                                            </a>
                                                                        </li>
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        @endcan

                                                        @can('Admin Add Attachments and Feedback')
                                                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                aria-haspopup="true">
                                                                <a href="{{ url('add_attachments_form') }}" class="menu-link">
                                                                    <span class="svg-icon menu-icon">
                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                fill-rule="evenodd">
                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                <path
                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                    fill="#000000" opacity="0.3" />
                                                                                <path
                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                    fill="#000000" />
                                                                            </g>
                                                                        </svg>
                                                                        <!--end::Svg Icon-->
                                                                    </span>
                                                                    <span class="menu-text">Add Attachments and Feedback</span>
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('List Cab Users')
                                                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                aria-haspopup="true">
                                                                <a href="{{ url('cab_users') }}" class="menu-link">
                                                                    <span class="svg-icon menu-icon">
                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                fill-rule="evenodd">
                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                <path
                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                    fill="#000000" opacity="0.3" />
                                                                                <path
                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                    fill="#000000" />
                                                                            </g>
                                                                        </svg>
                                                                        <!--end::Svg Icon-->
                                                                    </span>
                                                                    <span class="menu-text">Cab Users</span>
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('shifting CRS')
                                                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                aria-haspopup="true">
                                                                <a href="{{ url('/change-requests/reorder/home') }}" class="menu-link">
                                                                    <span class="svg-icon menu-icon">
                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                fill-rule="evenodd">
                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                <path
                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                    fill="#000000" opacity="0.3" />
                                                                                <path
                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                    fill="#000000" />
                                                                            </g>
                                                                        </svg>
                                                                        <!--end::Svg Icon-->
                                                                    </span>
                                                                    <span class="menu-text">Shifting CRS</span>
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('Final Confirmation')
                                                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                                aria-haspopup="true">
                                                                <a href="{{ route('final_confirmation.index') }}" class="menu-link">
                                                                    <span class="svg-icon menu-icon">
                                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                                fill-rule="evenodd">
                                                                                <rect x="0" y="0" width="24" height="24" />
                                                                                <path
                                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                                    fill="#000000" opacity="0.3" />
                                                                                <path
                                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                                    fill="#000000" />
                                                                            </g>
                                                                        </svg>
                                                                        <!--end::Svg Icon-->
                                                                    </span>
                                                                    <span class="menu-text">Final Confirmation</span>
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </li>
                                        @endcanany

                            @canany(['List change requests', 'Create ChangeRequest', 'My Assignments', 'Show My CRs'])
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                    aria-haspopup="true">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <span class="menu-text">Change Request</span>
                                        <span class="menu-desc"></span>
                                        <i class="la la-angle-down ml-2"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                        <ul class="menu-subnav">
                                            @can('Show My CRs')
                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                    aria-haspopup="true">
                                                    <a href="{{ url('change_request/listcrsbyuser') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">My Created CRs</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @php
                                                $crCount = 0;
                                                try {
                                                    if (auth()->check() && auth()->user()->can('CR Waiting Approval')) {
                                                        $crCount = app(App\Http\Repository\ChangeRequest\ChangeRequestRepository::class)->dvision_manager_cr()->count();
                                                    }
                                                } catch (\Exception $e) {
                                                    $crCount = 0;
                                                }
                                            @endphp
                                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                aria-haspopup="true">
                                                <a href="{{ url('change_request2/dvision_manager_cr') }}" class="menu-link">
                                                    <span class="svg-icon menu-icon">
                                                        <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24" />
                                                                <path
                                                                    d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                    fill="#000000" opacity="0.3" />
                                                                <path
                                                                    d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                    fill="#000000" />
                                                            </g>
                                                        </svg>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-text">CR Waiting Approval </span>
                                                </a>
                                            </li>

                                            @can('Show cr pending cap')
                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                    aria-haspopup="true">
                                                    <a href="{{ url('change_request2/cr_pending_cap') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">CR Pending Cab</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('show hold cr')
                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                    aria-haspopup="true">
                                                    <a href="{{ route('cr_hold') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">Hold CR</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('Create ChangeRequest')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('change_request/workflow/type') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">Create Change Request</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('List change requests')
                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                    aria-haspopup="true">
                                                    <a href="{{ url('change_request') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">List Change Request</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('My Assignments')
                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                    aria-haspopup="true">
                                                    <a href="{{ url('my_assignments') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">My Assignments</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('List Defects')
                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                    aria-haspopup="true">
                                                    <a href="{{ url('defects') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">Defects</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('List Assisstance Request')
                                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                                    aria-haspopup="true">
                                                    <a href="{{ url('prerequisites') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">
                                                            Assisstance Request
                                                        </span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endcanany
                            @canany(['Access Search', 'Access Advanced Search', 'Reports'])
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                    aria-haspopup="true">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <span class="menu-text">Search & Reporting</span>
                                        <i class="la la-angle-down ml-2"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                        <ul class="menu-subnav">
                                            @can('Access Search')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('searchs') }}" class="menu-link">
                                                        <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                        <span class="menu-text">Search</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('Access Advanced Search')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ route('advanced.search') }}" class="menu-link">
                                                        <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                        <span class="menu-text">Advanced Search</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('Reports')
                                                <li class="menu-item menu-item-submenu" data-menu-toggle="hover"
                                                    aria-haspopup="true">
                                                    <a href="javascript:;" class="menu-link menu-toggle">
                                                        <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                        <span class="menu-text">Reports</span>
                                                        <i class="la la-angle-down ml-2"></i>
                                                    </a>
                                                    <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                        <ul class="menu-subnav">
                                                            <li class="menu-item">
                                                                <a href="{{ url('reports/actual-vs-planned') }}"
                                                                    class="menu-link">
                                                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                    <span class="menu-text">Actual vs planned</span>
                                                                </a>
                                                            </li>
                                                            <li class="menu-item">
                                                                <a href="{{ url('reports/all-crs-by-requester') }}"
                                                                    class="menu-link">
                                                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                    <span class="menu-text">All CRs by requester</span>
                                                                </a>
                                                            </li>
                                                            <li class="menu-item">
                                                                <a href="{{ url('reports/cr-current-status') }}"
                                                                    class="menu-link">
                                                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                    <span class="menu-text">CR Current Status</span>
                                                                </a>
                                                            </li>
                                                            <li class="menu-item">
                                                                <a href="{{ url('reports/cr-crossed-sla') }}" class="menu-link">
                                                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                    <span class="menu-text">List of CRs crossed SLA</span>
                                                                </a>
                                                            </li>
                                                            <li class="menu-item">
                                                                <a href="{{ url('reports/rejected-crs') }}" class="menu-link">
                                                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                    <span class="menu-text">Rejected CRs</span>
                                                                </a>
                                                            </li>
                                                            <li class="menu-item">
                                                                <a href="{{ url('reports/sla-report') }}" class="menu-link">
                                                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                    <span class="menu-text">SLA Report</span>
                                                                </a>
                                                            </li>
                                                            <li class="menu-item">
                                                                <a href="{{ url('reports/kpi-report') }}" class="menu-link">
                                                                    <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                                    <span class="menu-text">KPI Report</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endcanany

                            @canany(['List Release', 'Release To CRs'])
                                <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                    aria-haspopup="true">
                                    <a href="javascript:;" class="menu-link menu-toggle">
                                        <span class="menu-text">Releases</span>
                                        <i class="la la-angle-down ml-2"></i>
                                    </a>
                                    <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                        <ul class="menu-subnav">
                                            @can('List Release')
                                                <li class="menu-item" aria-haspopup="true">
                                                    <a href="{{ url('releases') }}" class="menu-link">
                                                        <span class="svg-icon menu-icon">
                                                            <!--begin::Svg Icon | path:assets/media/svg/icons/General/Shield-check.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text">Releases</span>
                                                    </a>
                                                </li>
                                            @endcan

                                        </ul>
                                    </div>
                                </li>
                            @endcanany

                            </ul>
                            <!--end::Header Nav-->
                        </div>
                        <!--end::Header Menu-->
                    </div>
                    <!--end::Header Menu Wrapper-->
                </div>
                <!--end::Left-->
                <!--begin::Topbar-->
                <div class="topbar">
                    <!--begin::User-->
                    <!--begin::User Section-->
                    <!-- Group Bar (Left) -->
                    <div class="topbar-item">
                        <div class="btn-group">
                            <button type="button"
                                class="btn btn-icon btn-hover-transparent-white d-flex align-items-center btn-lg px-md-2 w-md-auto dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="navi-text" id="group-name">
                                    <img src="{{ asset('public/new_theme/assets/media/group.png') }}" />
                                    @if(session()->has('current_group_name'))
                                        {{ session('current_group_name') }}
                                    @else
                                        @if(auth()->user()->default_group)
                                            {{ auth()->user()->defualt_group->name }}
                                        @endif
                                    @endif
                                </span>
                                <i class="la text-white ml-2"></i>
                            </button>

                            <div class="dropdown-menu">
                                <!-- Loop through user's groups and list them -->
                                @if(isset($userGroups))
                                    @foreach ($userGroups as $group)
                                        @if(Session::has('current_group') && Session::get('current_group') == $group->group->id)
                                            <a class="dropdown-item {{ Session::has('current_group') && Session::get('current_group') == $group->group->id ? 'active' : '' }}"
                                                href="{{route('change_request.selectUserGroup', ['group' => $group->group->id])}}">
                                                {{ $group->group->name }}
                                            </a>
                                        @else
                                            <a class="dropdown-item {{ auth()->user()->defualt_group->id == $group->group->id ? 'active' : '' }} "
                                                href="{{route('change_request.selectUserGroup', ['group' => $group->group->id])}}">
                                                {{ $group->group->name }}
                                            </a>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach (auth()->user()->user_groups()->with('group')->get() as $group)
                                        @if(Session::has('current_group') && Session::get('current_group') == $group->group->id)
                                            <a class="dropdown-item {{ Session::has('current_group') && Session::get('current_group') == $group->group->id ? 'active' : '' }}"
                                                href="{{route('change_request.selectUserGroup', ['group' => $group->group->id])}}">
                                                {{ Session::get('current_group_name') }}
                                            </a>
                                        @else
                                            <a class="dropdown-item "
                                                href="{{route('change_request.selectUserGroup', ['group' => $group->group->id])}}">
                                                {{ $group->group->name }}
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- User Section (Right) -->
                    <div class="topbar-item ml-auto">
                        <div class="btn btn-icon btn-hover-transparent-white d-flex align-items-center btn-lg px-md-2 w-md-auto"
                            id="kt_quick_user_toggle">
                            <span
                                class="text-white opacity-70 font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                            <span
                                class="text-white opacity-90 font-weight-bolder font-size-base d-none d-md-inline mr-4">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <span class="symbol symbol-35">
                                @php
                                    $user_name = explode(" ", auth()->user()->name);
                                    $first_letters = "";
                                    foreach ($user_name as $word) {
                                        $first_letters .= mb_substr($word, 0, 1);
                                    }
                                @endphp
                                <span
                                    class="symbol-label text-white font-size-h5 font-weight-bold bg-white-o-30">{{$first_letters}}</span>
                            </span>
                            <i class="la text-white ml-2"></i>
                        </div>
                    </div>
                    <!--end::User Section-->
                    <!--end::Topbar-->
                    <!--end: Group bar -->
                </div>
                <!--end::Topbar-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Header-->