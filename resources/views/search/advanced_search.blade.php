@extends('layouts.app')

@section('content')

    @php
        $user_group = session()->has('current_group') ? session('current_group') : auth()->user()->defualt_group->id;
        $user_group = \App\Models\Group::find($user_group);

        $user_roles_names = auth()->user()->roles->pluck('name');
        $current_user_is_just_a_viewer = count($user_roles_names) === 1 && $user_roles_names[0] === 'Viewer';
    @endphp

        <!--begin::Content-->
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <!--begin::Entry-->
            <div class="d-flex flex-column-fluid">
                <!--begin::Container-->
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <!--begin::Card-->
                            <div class="card card-custom gutter-b shadow-sm border-0">
                                <div class="card-header border-0 py-5">
                                    <h3 class="card-title font-weight-bolder text-dark">
                                        <span class="card-icon mr-2">
                                            <span class="svg-icon svg-icon-lg svg-icon-primary">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Search.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                        <path
                                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                                            fill="#000000" fill-rule="nonzero" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </span>
                                        {{ $form_title }}
                                        <span class="text-muted font-weight-normal font-size-sm ml-2">Click arrow to expand search criteria</span>
                                    </h3>
                                    <div class="card-toolbar">
                                        <a href="#" id="advancedSearchToggle" class="btn btn-icon btn-sm btn-light-primary mr-1" data-toggle="collapse" data-target="#advancedSearchCollapse" aria-expanded="true">
                                            <i class="ki ki-arrow-up icon-nm"></i>
                                        </a>
                                    </div>
                                </div>
                                <!--begin::Form-->
                                <form id="advanced_search">
                                    <div id="advancedSearchCollapse" class="collapse show">
                                    @if (count($fields) > 0)
                                        <div class="card-body py-0">
                                            <div class="row">
                                                @php $createdField = null;
                                                $updatedField = null; @endphp
                                                    @foreach ($fields as $field)
                                                        @if (isset($field->custom_field))
                                                            @php
                                                                $customField = $field->custom_field;
                                                                // Change default col-sm-3 to col-lg-3 col-md-4 col-sm-6 for better responsiveness
                                                                $baseClasses = 'col-lg-3 col-md-4 col-sm-6';
                                                                $fieldClasses = isset($field->styleClasses) ? str_replace('col-sm-3', $baseClasses, $field->styleClasses) : $baseClasses . ' field-select';

                                                                $labelLower = isset($customField->label) ? strtolower(trim($customField->label)) : '';
                                                                // Skip deprecated standalone date filters
                                                                if (in_array($labelLower, ['less than date', 'greater than date'])) {
                                                                    continue;
                                                                }
                                                                // Defaults for rendering
                                                                $renderName = $customField->name;
                                                                $renderLabel = $customField->label;
                                                                // Remap CR ID -> CR No. with input name cr_no
                                                                if ($labelLower === 'cr id' || strtolower($customField->name) === 'cr_id') {
                                                                    $renderName = 'cr_no';
                                                                    $renderLabel = 'CR No.';
                                                                }

                                                                if (in_array($customField->name, ['created_at', 'updated_at'])) {
                                                                    $renderLabel = $customField->name === 'created_at' ? 'Creation Date' : 'Updated Date';
                                                                }

                                                            @endphp

                                                            <div @class([
                                                                'form-group modern-form-group',
                                                                $fieldClasses,
                                                                'col-12 date-range-group' => in_array($customField->name, ['created_at', 'updated_at']), // Give date ranges full width or larger
                                                                'col-lg-6' => in_array($customField->name, ['created_at', 'updated_at']), // Override for date ranges
                                                            ])>
                                                                <label class="font-weight-bold text-dark mb-2 {{ in_array($customField->name, ['created_at', 'updated_at']) ? 'd-block' : '' }}" for="{{ $renderName }}">{{ $renderLabel }}</label>

                                                                @if (in_array($customField->name, ['created_at', 'updated_at']))
                                                                    <div class="p-4 border-0 rounded bg-light-primary d-flex align-items-center justify-content-between date-range-container">
                                                                        <div class="d-flex flex-column flex-sm-row w-100 align-items-center">
                                                                            <div class="position-relative w-100 mr-sm-2 mb-2 mb-sm-0">
                                                                                <input
                                                                                    type="date"
                                                                                    class="form-control form-control-solid modern-form-control"
                                                                                    id="{{ $customField->name }}_start"
                                                                                    name="{{ $customField->name }}_start"
                                                                                    placeholder="Start date"
                                                                                    value="{{ request()->query($customField->name . '_start') }}"
                                                                                >
                                                                            </div>
                                                                            <span class="text-muted font-weight-bold mx-2">to</span>
                                                                            <div class="position-relative w-100 ml-sm-2">
                                                                                <input
                                                                                    type="date"
                                                                                    class="form-control form-control-solid modern-form-control"
                                                                                    id="{{ $customField->name }}_end"
                                                                                    name="{{ $customField->name }}_end"
                                                                                    placeholder="End date"
                                                                                    value="{{ request()->query($customField->name . '_end') }}"
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="updated_at_error" class="invalid-feedback d-none"></div>
                                                                @elseif ($customField->type == 'select')
                                                                    <select
                                                                        class="form-control form-control-solid select2"
                                                                        id="{{ $renderName }}"
                                                                        name="{{ $renderName }}[]"
                                                                        multiple
                                                                        data-placeholder="Select {{ $renderLabel }}"
                                                                        style="width:100%;"
                                                                    >
                                                                    @php
                                                                        $selectedValuesRaw = request()->query($renderName, []);
                                                                        if (!is_array($selectedValuesRaw)) {
                                                                            $selectedValuesRaw = strlen((string) $selectedValuesRaw) ? explode(',', (string) $selectedValuesRaw) : [];
                                                                        }
                                                                        $selectedValues = array_map('strval', $selectedValuesRaw);
                                                                    @endphp
                                                                    <!-- options generated below -->
                                                                    @if($customField->name == "new_status_id")
                                                                        @foreach ($statuses as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->status_name }}</option>
                                                                        @endforeach
                                                                    @endif

                                                                    @if($customField->name == "priority_id")
                                                                        @foreach ($priorities as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                                        @endforeach
                                                                    @endif


                                                                    @if($customField->name == "application_id")
                                                                        @foreach ($applications as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                                        @endforeach
                                                                    @endif


                                                                    @if($customField->name == "parent_id")
                                                                        @foreach ($parents as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                                        @endforeach
                                                                    @endif

                                                                    @if($customField->name == "cr_type")
                                                                        @foreach ($cr_types as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                                        @endforeach
                                                                    @endif

                                                                    @if($customField->name == "category_id")
                                                                        @foreach ($categories as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                    @if($customField->name == "unit_id")
                                                                        @foreach ($units as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                    @if($customField->name == "workflow_type_id")
                                                                        @foreach ($workflows as $value)
                                                                            @php $isSelected = in_array((string) $value->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $value->id }}" @if($isSelected) selected @endif>{{ $value->name }}</option>
                                                                        @endforeach
                                                                    @endif

                                                                    @if($customField->name === 'tester_id')
                                                                        @foreach ($testing_users as $testing_user)
                                                                            @php $isSelected = in_array((string) $testing_user->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $testing_user->id }}" @if($isSelected) selected @endif>{{ $testing_user->user_name }}</option>
                                                                        @endforeach
                                                                    @endif

                                                                    @if($customField->name === 'designer_id')
                                                                        @foreach ($sa_users as $sa_user)
                                                                            @php $isSelected = in_array((string) $sa_user->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $sa_user->id }}" @if($isSelected) selected @endif>{{ $sa_user->user_name }}</option>
                                                                        @endforeach
                                                                    @endif

                                                                    @if($customField->name === 'developer_id')
                                                                        @foreach ($developer_users as $developer_user)
                                                                            @php $isSelected = in_array((string) $developer_user->id, $selectedValues, true); @endphp
                                                                            <option value="{{ $developer_user->id }}" @if($isSelected) selected @endif>{{ $developer_user->user_name }}</option>
                                                                        @endforeach
                                                                    @endif

                                                                    </select>
                                                                @elseif ($customField->type == 'textArea')
                                                                    <textarea
                                                                        class="form-control form-control-solid modern-form-control"
                                                                        id="{{ $renderName }}"
                                                                        name="{{ $renderName }}"
                                                                        placeholder="{{ $renderLabel }}"
                                                                        rows="4"
                                                                    >{{ request()->query($renderName) }}</textarea>
                                                                @elseif ($customField->type == 'text' || $customField->type == 'input')

                                                                    @php $isCrIdField = in_array(strtolower($customField->name), ['cr_id', 'id']) || ($labelLower === 'cr id'); @endphp
                                                                    <input
                                                                        type="{{ $isCrIdField ? 'number' : 'text' }}"
                                                                        class="form-control form-control-solid modern-form-control"
                                                                        id="{{ $renderName }}"
                                                                        name="{{ $renderName }}"
                                                                        placeholder="{{ $renderLabel }}"
                                                                        value="{{ request()->query($renderName) }}"
                                                                    >


                                                                @elseif ($customField->type == 'number')
                                                                    <input
                                                                        type="number"
                                                                        class="form-control form-control-solid modern-form-control"
                                                                        id="{{ $renderName }}"
                                                                        name="{{ $renderName }}"
                                                                        placeholder="{{ $renderLabel }}"
                                                                        value="{{ request()->query($renderName) }}"
                                                                    >
                                                                @elseif ($customField->type == 'date')
                                                                    <input
                                                                        type="date"
                                                                        class="form-control form-control-solid modern-form-control"
                                                                        id="{{ $customField->name }}"
                                                                        name="{{ $customField->name }}"
                                                                        value="{{ request()->query($customField->name) }}"
                                                                    >
                                                                @elseif ($customField->type == 'checkbox')
                                                                    <div class="pt-2">
                                                                        <label class="modern-checkbox">
                                                                            <input
                                                                                type="checkbox"
                                                                                name="{{ $renderName }}"
                                                                                value="1"
                                                                                {{ request()->query($renderName) == '1' ? 'checked' : '' }}
                                                                            >
                                                                            <span class="checkmark"></span>
                                                                        </label>
                                                                    </div>
                                                                @endif



                                                                @if(isset($customField->required) && $customField->required)
                                                                    <span class="text-danger">*</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <p>Custom field data is not available.</p>
                                                        @endif
                                                    @endforeach
                                                    </div>
                                                </div>

                                                <div class="card-footer border-0 p-5 d-flex justify-content-end bg-white">
                                                    <button type="button" id="reset_advanced_search" class="btn btn-secondary font-weight-bold mr-3 px-6 h-40px">
                                                        <i class="la la-trash"></i> Clear
                                                    </button>
                                                    <button type="submit" class="btn btn-primary font-weight-bold px-8 h-40px shadow-sm">
                                                        <i class="la la-search"></i> Search
                                                    </button>
                                                </div>
                                    @else
                                        <div class="card-body">
                                            <p class="text-center text-muted">No fields available for search.</p>
                                        </div>
                                    @endif
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

            <div class="container" id="results">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b shadow-sm border-0">
                            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                                <div class="card-title">
                                    <h3 class="card-label">Search Results
                                    <span class="d-block text-muted pt-2 font-size-sm">Total Records: {{ $items->total() }}</span></h3>
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
                                        <!-- Simple search dropdown placeholder if needed, but in advanced search we usually just export -->
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <!--begin: Datatable-->
                                <div class="table-responsive">
                                <table class="table table-bordered">
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
                                                <th>Top Management</th>
                                                <th>On Hold</th>
                                                <th>Last Action Date</th>
                                                <th>Action</th>
                                            @else
                                                        <tr class="text-uppercase text-muted">
                                                <th style="min-width: 110px">CR ID</th>
                                                <th style="min-width: 150px">Title</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($items as $cr)
                                        @include("search.loop")
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
                            </div>
                            <!--end::Card-->
                        </div>
                    </div>
                </div>
            </div>
@endsection
@push('css')
    {{--Avoid horizontal scrollbars when Select2 opens--}}
    <style>
        html,body{overflow-x:hidden}
        .select2-container{max-width:100%}
        .select2-dropdown{max-width:100vw;overflow-x:hidden}
        .modern-form-group label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #7E8299;
        }
        .card-title .card-icon {
            margin-right: 0.75rem;
        }
        .table-head-custom th {
            background-color: #F3F6F9 !important;
            color: #464E5F !important;
            border-bottom: 0 !important;
            padding-top: 1.2rem !important;
            padding-bottom: 1.2rem !important;
        }
        .btn-light-secondary {
            color: #7E8299;
            background-color: #F3F6F9;
            border-color: #F3F6F9;
        }
        .btn-light-secondary:hover {
            color: #3F4254;
            background-color: #E4E6EF;
            border-color: #E4E6EF;
        }
    </style>
@endpush
@push('script')
    <script>
    $(document).ready(function() {
        $('#advancedSearchCollapse').on('show.bs.collapse', function () {
            $('#advancedSearchToggle i').removeClass('ki-arrow-down').addClass('ki-arrow-up');
        });

        $('#advancedSearchCollapse').on('hide.bs.collapse', function () {
            $('#advancedSearchToggle i').removeClass('ki-arrow-up').addClass('ki-arrow-down');
        });

        // Close animation on load to hint user
        setTimeout(function() {
            $('#advancedSearchCollapse').collapse('hide');
        }, 2000);
    });

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
      }
    });

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
            fullDescription = $('<div>').html(fullDescription).text();
            fullDescription = JSON.parse(fullDescription);
        } catch (e) {
            console.warn('Failed to parse description JSON:', e);
        }
        $('#descriptionModal .modal-body').text(fullDescription);
        $('#descriptionModal').modal('show');
    });
    </script>
    <script>
        @if(count(request()->query()))
            $('html, body').animate({
                scrollTop: $('#results').offset().top - 200
            }, 800);
        @endif
        function checkFields(form) {
            var  inputs = $('.advanced_search_field');
            var filled = inputs.filter(function(){
                return $(this).val()  !== "";
            });
            return filled.length !== 0;
        }
    </script>
    <script>
        // Avoid horizontal scrollbars when Select2 opens
        $(function(){
            if ($.fn.select2) {
                $('.select2').each(function(){
                    var $el = $(this);
                    $el.select2({
                        placeholder: $el.data('placeholder') || 'Select',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#advanced_search')
                    });
                });
            }

            function clearDateValidation(ids){
                ids.forEach(function(id){
                    var $i = $('#'+id);
                    $i.removeClass('is-invalid');
                });
            }

            function setError(elId, message){
                var $el = $('#'+elId);
                if(!$el.length) return;
                if(message){
                    $el.text(message).removeClass('d-none');
                } else {
                    $el.text('').addClass('d-none');
                }
            }

            function markInvalid(ids){
                ids.forEach(function(id){
                    var $i = $('#'+id);
                    $i.addClass('is-invalid');
                });
            }

            function validateRange(startId, endId, label, errorElId){
                var s = $('#'+startId).val();
                var e = $('#'+endId).val();
                clearDateValidation([startId, endId]);
                setError(errorElId, '');
                if (!s || !e) return true; // only validate when both filled
                var sd = new Date(s);
                var ed = new Date(e);
                if (isNaN(sd.getTime()) || isNaN(ed.getTime())) return true;
                if (sd.getTime() > ed.getTime()){
                    var msg = label + ' range is invalid: start must be before or equal to end.';
                    if (window.toastr && toastr.error){ toastr.error(msg); }
                    else { alert(msg); }
                    markInvalid([startId, endId]);
                    setError(errorElId, msg);
                    return false;
                }
                return true;
            }

            function syncBounds(startId, endId){
                var s = $('#'+startId).val();
                var e = $('#'+endId).val();
                // Set native constraints
                if (s){ $('#'+endId).attr('min', s); } else { $('#'+endId).removeAttr('min'); }
                if (e){ $('#'+startId).attr('max', e); } else { $('#'+startId).removeAttr('max'); }
            }

            $('#advanced_search').on('submit', function(e){
                var ok1 = validateRange('created_at_start','created_at_end','Creation Date','created_at_error');
                var ok2 = validateRange('updated_at_start','updated_at_end','Updated Date','updated_at_error');
                if (!(ok1 && ok2)){
                    e.preventDefault();
                }
            });

            // Real-time validation on input
            $('#created_at_start, #created_at_end').on('change input', function(){
                syncBounds('created_at_start','created_at_end');
                validateRange('created_at_start','created_at_end','Creation Date','created_at_error');
            });
            $('#updated_at_start, #updated_at_end').on('change input', function(){
                syncBounds('updated_at_start','updated_at_end');
                validateRange('updated_at_start','updated_at_end','Updated Date','updated_at_error');
            });

            // Hydrate constraints on load
            syncBounds('created_at_start','created_at_end');
            syncBounds('updated_at_start','updated_at_end');

            $('#reset_advanced_search').on('click', function(){
                var $form = $('#advanced_search');
                if ($form.length && $form[0]) {
                    $form[0].reset();
                }
                $form.find('input[type="text"], input[type="number"], input[type="date"], input[type="email"], input[type="search"], textarea').val('').trigger('change');
                // Reset Select2 fields explicitly
                $form.find('select.select2').val(null).trigger('change');
                // Clear date errors
                setError('created_at_error','');
                setError('updated_at_error','');
                clearDateValidation(['created_at_start','created_at_end','updated_at_start','updated_at_end']);
                // Clear constraints
                $('#created_at_start, #created_at_end, #updated_at_start, #updated_at_end').removeAttr('min').removeAttr('max');
            });
        });
    </script>
@endpush
