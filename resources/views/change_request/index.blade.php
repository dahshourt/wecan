@extends('layouts.app')

@section('content')
    @php
        $roles_name = auth()->user()->roles->pluck('name');
        $user_group = session()->has('current_group') ? session('current_group') : auth()->user()->defualt_group->id;
        $user_group =\App\Models\Group::find($user_group);
        $user_is_not_viewer = ! ($roles_name->count() === 1 && $roles_name->contains('Viewer'));
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
                        <div class="card-title d-flex align-items-center justify-content-between w-100">
                            <h3 class="card-label mb-0">{{ $title }}</h3>
                            <div class="card-toolbar">
                                @can('Create ChangeRequest')
                                    <!--begin::Button-->
                                    <a href='{{ url("$route/workflow/type") }}' class="btn btn-primary font-weight-bolder">
                                        <span class="svg-icon svg-icon-md">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                 height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <circle fill="#000000" cx="9" cy="15" r="6"/>
                                                    <path
                                                        d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
                                                        fill="#000000" opacity="0.3"/>
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>New Record</a>
                                    <!--end::Button-->
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            // Prepare group meta (labels + workflow ids per group + total counts)
                            $groupTabs = [];
                            foreach ($crs_by_user_groups_by_workflow as $groupKey => $workflowsData) {
                                if ($groupKey === 'all') {
                                    $label = 'All Groups';
                                } else {
                                    $groupModel = $user_groups->firstWhere('id', (int) $groupKey);
                                    $label = $groupModel ? $groupModel->title : 'Group #' . $groupKey;
                                }

                                // Calculate total count across all workflows in this group (memory efficient)
                                $totalCount = 0;
                                foreach ($workflowsData ?? [] as $workflowId => $collection) {
                                    if ($collection && method_exists($collection, 'total')) {
                                        $totalCount += $collection->total();
                                    }
                                }

                                $groupTabs[$groupKey] = [
                                    'label' => $label,
                                    'workflow_ids' => array_keys($workflowsData ?? []),
                                    'total_count' => $totalCount,
                                ];
                            }

                            $groupKeys = array_keys($groupTabs);

                            // Parse URL to find active group and workflow
                            $activeGroupKey = null;
                            $activeWorkflowId = null;

                            // Check all query parameters for type_{groupKey}_{workflowId} pattern
                            $queryParams = request()->query();
                            foreach ($queryParams as $key => $value) {
                                if (strpos($key, 'type_') === 0) {
                                    // Pattern: type_{groupKey}_{workflowId}
                                    // Example: type_all_3 or type_12_5
                                    $parts = explode('_', $key);
                                    if (count($parts) >= 3) {
                                        // Extract groupKey (could be "all" or a number like "12")
                                        $extractedGroupKey = $parts[1];
                                        $extractedWorkflowId = (int) $parts[2];
                                        
                                        // Check if this group key exists in our tabs
                                        // Handle both string and numeric comparisons
                                        foreach ($groupKeys as $gk) {
                                            if ((string)$gk === (string)$extractedGroupKey) {
                                                $activeGroupKey = $gk; // Use the actual key from array
                                                $activeWorkflowId = $extractedWorkflowId;
                                                break 2; // Break out of both loops
                                            }
                                        }
                                    }
                                }
                            }

                            // Default to first group and first workflow if not found or invalid
                            if (!$activeGroupKey || !array_key_exists($activeGroupKey, $groupTabs)) {
                                $activeGroupKey = $groupKeys[0] ?? null;
                            }

                            // If we have a group but no workflow, use first workflow of that group
                            if ($activeGroupKey && !$activeWorkflowId) {
                                $firstWorkflowId = $groupTabs[$activeGroupKey]['workflow_ids'][0] ?? null;
                                $activeWorkflowId = $firstWorkflowId ? (int) $firstWorkflowId : null;
                            }

                            // Validate workflow exists in the active group
                            if ($activeGroupKey && $activeWorkflowId) {
                                $validWorkflows = $groupTabs[$activeGroupKey]['workflow_ids'] ?? [];
                                // Convert to integers for comparison
                                $validWorkflows = array_map('intval', $validWorkflows);
                                if (!in_array($activeWorkflowId, $validWorkflows, true)) {
                                    $activeWorkflowId = (int) ($validWorkflows[0] ?? null);
                                }
                            }
                        @endphp

                        @if(!empty($groupTabs))
                            <!--begin::Group Tabs (top level, client-side like Top Management CRS)-->
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-2x nav-tabs-line-primary mb-5" role="tablist">
                                @foreach($groupTabs as $groupKey => $info)
                                    @php
                                        $firstWorkflowIdForGroup = $info['workflow_ids'][0] ?? null;
                                    @endphp
                                    <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center {{ $groupKey === $activeGroupKey ? 'active' : '' }}"
                                           data-toggle="tab"
                                           href="#group_tab_{{ $groupKey }}"
                                           role="tab"
                                           aria-selected="{{ $groupKey === $activeGroupKey ? 'true' : 'false' }}"
                                           data-group="{{ $groupKey }}"
                                           @if($firstWorkflowIdForGroup)
                                               data-first-workflow="{{ $firstWorkflowIdForGroup }}"
                                           @endif
                                        >
                                            <span class="nav-icon mr-2">
                                                <i class="flaticon2-layers-1 icon-lg"></i>
                                            </span>
                                            <span class="nav-text font-weight-bolder">{{ $info['label'] }}</span>
                                            @if($info['total_count'] > 0)
                                                <span class="label label-light-{{ $groupKey === $activeGroupKey ? 'primary' : 'dark' }}-inline label-pill font-weight-bold ml-2">
                                                    {{ $info['total_count'] }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <!--end::Group Tabs-->

                            <div class="tab-content">
                                @foreach($groupTabs as $groupKey => $info)
                                    @php
                                        $workflowsIdsForGroup = $info['workflow_ids'];
                                        $workflowsForGroup = $active_work_flows->whereIn('id', $workflowsIdsForGroup);
                                    @endphp
                                    <div class="tab-pane fade {{ $groupKey === $activeGroupKey ? 'show active' : '' }}"
                                         id="group_tab_{{ $groupKey }}"
                                         role="tabpanel">

                                        @if($workflowsForGroup->count() > 0)
                                            <!--begin::Workflow Tabs inside group (like old design)-->
                                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-primary mb-5"
                                                role="tablist">
                                                @foreach($workflowsForGroup as $workflow)
                                                    @php
                                                        $isActiveWorkflow = ($groupKey === $activeGroupKey && $workflow->id === $activeWorkflowId);
                                                        $collection = $crs_by_user_groups_by_workflow[$groupKey][$workflow->id] ?? null;
                                                        $workflowCount = $collection && method_exists($collection, 'total') ? $collection->total() : 0;
                                                    @endphp
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ $isActiveWorkflow ? 'active' : '' }}"
                                                           data-toggle="tab"
                                                           href="#workflow_tab_{{ $groupKey }}_{{ $workflow->id }}"
                                                           role="tab"
                                                           aria-selected="{{ $isActiveWorkflow ? 'true' : 'false' }}"
                                                           data-group="{{ $groupKey }}"
                                                           data-workflow="{{ $workflow->id }}">
                                                            <span class="nav-text font-weight-bold">{{ $workflow->name }}</span>
                                                            @if($workflowCount > 0)
                                                                <span class="label label-light-{{ $isActiveWorkflow ? 'primary' : 'dark' }}-inline label-pill font-weight-bold ml-2">
                                                                    {{ $workflowCount }}
                                                                </span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <!--end::Workflow Tabs-->

                                            <!--begin::Workflow Tab Content-->
                                            <div class="tab-content">
                                                @foreach($workflowsForGroup as $workflow)
                                                    @php
                                                        $collection = $crs_by_user_groups_by_workflow[$groupKey][$workflow->id] ?? null;
                                                        $isActiveWorkflow = ($groupKey === $activeGroupKey && $workflow->id === $activeWorkflowId);
                                                    @endphp
                                                    <div class="tab-pane fade {{ $isActiveWorkflow ? 'show active' : '' }}"
                                                         id="workflow_tab_{{ $groupKey }}_{{ $workflow->id }}"
                                                         role="tabpanel">
                                                        @if($collection && $collection->count() > 0)
                                                            @if($workflow->id === 3)
                                                                <x-crs.in-house :is-not-viewer="$user_is_not_viewer" :user-group="$user_group" :collection="$collection" />
                                                            @elseif($workflow->id === 5)
                                                                <x-crs.vendor :is-not-viewer="$user_is_not_viewer" :user-group="$user_group" :collection="$collection" />
                                                            @elseif($workflow->id === 9)
                                                                <x-crs.promo :is-not-viewer="$user_is_not_viewer" :user-group="$user_group" :collection="$collection" />
                                                            @endif

                                                            <!--begin: Pagination-->
                                                            <div class="d-flex justify-content-center mt-5">
                                                                {{ $collection->links() }}
                                                            </div>
                                                            <!--end: Pagination-->
                                                        @else
                                                            <div class="alert alert-light text-center" role="alert">
                                                                <i class="la la-inbox text-muted" style="font-size: 3rem;"></i>
                                                                <h4 class="text-muted mt-3">No Change Requests Found</h4>
                                                                <p class="text-muted mb-0">
                                                                    There are currently no change requests available for this group and workflow.
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!--end::Workflow Tab Content-->
                                        @else
                                            <div class="alert alert-light text-center" role="alert">
                                                <i class="la la-inbox text-muted" style="font-size: 3rem;"></i>
                                                <h4 class="text-muted mt-3">No Change Requests Found</h4>
                                                <p class="text-muted mb-0">There are currently no change requests available for this group.</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!--begin: No Data State-->
                            <div class="alert alert-light text-center" role="alert">
                                <i class="la la-inbox text-muted" style="font-size: 3rem;"></i>
                                <h4 class="text-muted mt-3">No Change Requests Found</h4>
                                <p class="text-muted mb-0">There are currently no change requests available.</p>
                            </div>
                            <!--end: No Data State-->
                        @endif
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Entry-->
        </div>
        <!--end::Content-->
    </div>

@endsection

@push('css')
    <style>
        /* Enhanced Tab Styling */
        .nav-tabs-line-3x {
            padding: 0.5rem 0;
            margin-bottom: 2rem;
        }

        .nav-tabs-line-3x .nav-item {
            margin-right: 0.75rem;
        }

        .nav-tabs-line-3x .nav-link {
            font-size: 1rem;
            padding: 1rem !important;
            color: #000000 !important;
            transition: all 0.3s ease;
            background-color: #e4e6ef !important;
            border-radius: 0.5rem 0.5rem 0 0;
            margin-bottom: -1px;
        }

        .nav-tabs-line-3x .nav-link:hover {
            color: #3699ff;
            background-color: #f1f8ff;
            transform: translateY(-2px);
        }

        .nav-tabs-line-3x .nav-link.active {
            color: #ffffff !important;
            font-weight: 600;
            background-color: #3699ff !important;
            border-radius: 0.5rem 0.5rem 0 0;
            box-shadow: 0 4px 12px rgba(54, 153, 255, 0.25);
            border-color: #3699ff;
            padding: 1.5rem 3.5rem;
            transform: translateY(-2px);
        }

        .nav-tabs-line-3x .nav-link.active .nav-text {
            color: #ffffff !important;
        }

        .nav-tabs-line-3x .nav-link .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.42rem;
        }

        /* Tab content styling */
        .tab-content {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Empty state styling */
        .alert-light {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            padding: 2rem;
        }
    </style>
@endpush

@push('script')

    <script>
        $(function () {
            $("#example1").DataTable({
                'responsive': false,
                'lengthChange': false,
                'autoWidth': true,
                'ordering': false,
                'buttons': ['excel']
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                'paging': false,
                'lengthChange': false,
                'searching': false,
                'ordering': true,
                'info': false,
                'autoWidth': true,
                'responsive': false,
                'scrollX': true,
                order: [[0, 'desc']]

            });
        });

    </script>

    <script>
        $(document).on('click', '.js-toggle-cr-details', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('cr-id');
            var $row = $btn.closest('tr');
            console.log("clicked", id, $row);
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
            }
        });

        $(document).on('click', 'tr.cr-row', function (e) {
            if ($(e.target).closest('a, button, .js-toggle-cr-details, .dropdown-menu, .select2-container').length) {
                return;
            }
            $(this).find('.js-toggle-cr-details').trigger('click');
        });
        $(function () {
            $('tr.cr-row:first').find('.js-toggle-cr-details').trigger('click');
        });
    </script>

    <script>
        // Tab persistence and pagination handling using pageName: type_{groupKey}_{workflowId}
        // Note: Initial tab activation is now handled server-side by PHP
        // JavaScript only handles dynamic tab switching and URL updates
        $(document).ready(function () {
            // When switching workflow tabs, update URL to keep only the current type_{group}_{workflow} param
            $(document).on('shown.bs.tab', '.nav-tabs a[data-workflow]', function (e) {
                const $tab = $(e.target);
                const groupKey = $tab.data('group');
                const workflowId = $tab.data('workflow');

                if (!groupKey || !workflowId) {
                    return;
                }

                // Update badge colors for workflow tabs in this group
                $('a[data-group="' + groupKey + '"][data-workflow]').each(function() {
                    if ($(this).is($tab)) {
                        $(this).find('.label').removeClass('label-light-dark-inline').addClass('label-light-primary-inline');
                    } else {
                        $(this).find('.label').removeClass('label-light-primary-inline').addClass('label-light-dark-inline');
                    }
                });

                const currentParams = new URLSearchParams(window.location.search);
                const newParams = new URLSearchParams();

                // Preserve all non-type_* params
                for (let [key, value] of currentParams.entries()) {
                    if (!key.startsWith('type_')) {
                        newParams.append(key, value);
                    }
                }

                // Add the new type_{group}_{workflow} param with page 1
                newParams.set('type_' + groupKey + '_' + workflowId, '1');

                const newUrl = window.location.pathname + '?' + newParams.toString();
                window.history.pushState({path: newUrl}, '', newUrl);
            });

            // When switching group tabs, also update URL and ensure first workflow of that group is selected
            $(document).on('shown.bs.tab', '.nav-tabs a[data-group]:not([data-workflow])', function (e) {
                const $tab = $(e.target);
                const groupKey = $tab.data('group');
                const firstWorkflowId = $tab.data('first-workflow');

                if (!groupKey || !firstWorkflowId) {
                    return;
                }

                // Update badge colors for group tabs
                $('.nav-tabs a[href^="#group_tab_"]').each(function() {
                    if ($(this).is($tab)) {
                        $(this).find('.label').removeClass('label-light-dark-inline').addClass('label-light-primary-inline');
                    } else {
                        $(this).find('.label').removeClass('label-light-primary-inline').addClass('label-light-dark-inline');
                    }
                });

                // Activate the first workflow tab inside this group
                const workflowTabSelector = 'a[data-group="' + groupKey + '"][data-workflow="' + firstWorkflowId + '"]';
                const workflowTab = $(workflowTabSelector);
                if (workflowTab.length) {
                    workflowTab.tab('show');
                }

                const currentParams = new URLSearchParams(window.location.search);
                const newParams = new URLSearchParams();

                // Preserve all non-type_* params
                for (let [key, value] of currentParams.entries()) {
                    if (!key.startsWith('type_')) {
                        newParams.append(key, value);
                    }
                }

                // Add the new type_{group}_{workflow} param with page 1
                newParams.set('type_' + groupKey + '_' + firstWorkflowId, '1');

                const newUrl = window.location.pathname + '?' + newParams.toString();
                window.history.pushState({path: newUrl}, '', newUrl);
            });
        });
    </script>
@endpush

