@extends('layouts.app')

@section('content')
    @push('css')
        <link href="{{asset('public/new_theme/assets/css/pages/home/home-custom.css')}}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    @endpush

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="dashboard-container">
            <!-- Greeting Section -->
            <div class="greeting-section">
                <div>
                    <h2 class="greeting-title">Good Morning, {{ explode(' ', trim(auth()->user()->name))[0] }} 👋</h2>
                    <div class="last-login-text">
                        <i class="flaticon-clock-1 last-login-icon"></i>
                        Last Login: {{ auth()->user()->last_login }}
                    </div>
                </div>
            </div>

            <!-- Hero Banner: Create CR -->
            @can('Create ChangeRequest')
                <div class="hero-banner">
                    <div class="hero-content">
                        <h3 class="hero-title">Create CR</h3>
                        <p class="hero-subtitle">Submit a new change in seconds</p>
                        <a href="{{ url('/change_request/workflow/type') }}" class="btn-hero-action">Create Now <i
                                class="flaticon2-next ml-2"></i></a>
                    </div>
                    <!-- 3D Illustration Area -->
                    <div class="hero-illustration">
                        <img src="{{ asset('public/new_theme/assets/media/svg/illustrations/working.svg') }}"
                            alt="Create CR" style="max-height: 200px; opacity: 0.9;">
                        <!-- Replaced missing design-release.svg with working.svg -->
                    </div>
                </div>
            @endcan

            <div class="actions-grid">
                <!-- Quick Search -->
                @can('Access Search')
                    <a href="{{ url('/searchs') }}" class="action-card text-decoration-none">
                        <div class="card-icon-wrapper">
                            <i class="flaticon-search card-icon"></i>
                        </div>
                        <h4 class="card-title">Quick Search</h4>
                        <p class="card-description">Locate specific change requests by ID, title, or requester name using our
                            search tool.</p>
                        <span class="card-action-link">Quick search <i class="flaticon2-next"></i></span>
                    </a>
                @endcan

                <!-- Advanced Filters -->
                @can('Access Advanced Search')
                    <a href="{{ url('/search/advanced_search') }}" class="action-card text-decoration-none">
                        <div class="card-icon-wrapper">
                            <i class="flaticon2-search-1 card-icon"></i>
                        </div>
                        <h4 class="card-title">Advanced Search</h4>
                        <p class="card-description">Drill down into specific departments, status transitions, or priority levels
                            for deep insights.</p>
                        <span class="card-action-link">Advance View <i class="flaticon2-next"></i></span>
                    </a>
                @endcan

                <!-- My Assignments -->
                @can('My Assignments')
                    <a href="{{ url('/my_assignments') }}" class="action-card text-decoration-none">
                        <div class="card-icon-wrapper">
                            <i class="flaticon-list-3 card-icon"></i>
                        </div>
                        <h4 class="card-title">My Assignments</h4>
                        <p class="card-description">Track change requests waiting for your approval or action in the current
                            sprint cycle.</p>
                        <span class="card-action-link">View Tasks <i class="flaticon2-next"></i></span>
                    </a>
                @endcan
            </div>

            <!-- KPI Chart (Preserved) -->
            @if($user_has_kpi_chart_permission)
                <!-- <div class="card card-custom gutter-b mt-10">
                                                            <div class="card-header border-0">
                                                                <div class="card-title">
                                                                    <h3 class="card-label">KPI Status Chart</h3>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="kpiChart" style="max-height: 400px;"></canvas>
                                                            </div>
                                                        </div> -->
            @endif
        </div>
    </div>
@endsection

@if($user_has_kpi_chart_permission)
    @push('script')

        <script src="{{ asset('js/charts.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var kpiData = @json($kpiData);

                var ctx = document.getElementById('kpiChart').getContext('2d');
                var kpiChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: kpiData.labels,
                        datasets: kpiData.datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'KPI Count by Year and Status'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                title: {
                                    display: true,
                                    text: 'Count'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Status'
                                }
                            }
                        }
                    }
                });
            });
        </script>

    @endpush
@endif