@php
    $fieldName = $item->CustomField->name;
    $fieldLabel = $item->CustomField->label;
    $isRequired = isset($item->validation_type_id) && $item->validation_type_id == 1;
    $isEnabled = isset($item->enable) && $item->enable == 1;
    $inputType = in_array($fieldName, ['division_manager', 'mds_approvers']) ? 'email' : 'text';
    $inputValue = isset($cr) ? old($fieldName, $custom_field_value ?? '') : old($fieldName);
@endphp

@if($item->CustomField->type == "multiselect")
    <div class="col-md-6 change-request-form-field field_{{ $item->CustomField->name }}">

        {{-- Smart label rendering --}}
        @php
            $durationFieldMap = [
                'tester_id' => 'test_duration',
                'designer_id' => 'design_duration',
                'developer_id' => 'develop_duration',
            ];
            $showLabelName = $durationFieldMap[$fieldName] ?? null;
        @endphp

        @if($showLabelName && isset($cr) && !empty($cr->{$durationFieldMap[$fieldName] ?? ''}))
            <label type="text" class="form-control form-control-lg"> 
                {{ $cr->{$fieldName == 'tester_id' ? 'tester' : ($fieldName == 'designer_id' ? 'designer' : 'developer')}->name ?? '' }} 
            </label>
        @else
            <label for="{{ $fieldName }}">{{ $item->CustomField->label }}</label>
        @endif

        @php
            // Check if field should be required (hide asterisk for cap_users in specific statuses)
            $shouldShowRequired = true;
            if ($fieldName === 'cap_users' && isset($cr)) {
                $currentStatus = $cr->getCurrentStatus()?->status?->status_name ?? '';
                $excludedStatuses = [
                    "Request Vendor MDS",
                    "Update CR MDs", 
                    "Pending Update CR MDs",
                    "Pending Validate CR MDs",
                    "Pending MDs Sign off",
                    "Reject and Re-validation CR",
                    "Need MDs Re-negotiote",
                    "Pending release Selection"
                ];
                $shouldShowRequired = !in_array($currentStatus, $excludedStatuses);
            }
        @endphp
        
        @if(isset($item->validation_type_id) && $item->validation_type_id == 1 && $shouldShowRequired)
            <span style="color: red;">*</span>
        @endif

        {{-- Special handling for specific fields --}}
        @if(!isset($cr) && $fieldName === 'application_id')
            <select name="{{ $fieldName }}" class="form-control form-control-lg select2-field" multiple>
                <option value="{{ $target_system->id }}">{{ $target_system->name }}</option>
            </select>
        @elseif($fieldName === 'cr_member')
            <select name="{{ $fieldName }}" class="form-control form-control-lg select2-field" multiple>
                <option value="">Select</option>
                @foreach($item->CustomField->getCustomFieldValue() as $value)
                    @if($value->defualt_group->title === 'CR Team Admin')
                        <option value="{{ $value->id }}" {{ isset($cr) && $cr->{$fieldName} == $value->id ? 'selected' : '' }}>
                            {{ $value->name }}
                        </option>
                    @endif
                @endforeach
            </select>

            
        @else
            @php
                $required = isset($item->validation_type_id) && $item->validation_type_id == 1 ? 'required' : '';
                $disabled = isset($item->enable) && $item->enable != 1 ? 'disabled' : '';
                $customOptions = $item->CustomField->getCustomFieldValue();
            @endphp

            <select name="{{ $fieldName }}[]" 
                    class="form-control form-control-lg select2-field" 
                    multiple="multiple" 
                    data-placeholder="Select {{ $fieldLabel }}"
                    {{ $required }} {{ $disabled }}
                    @cannot('Set Time For Another User')
                        @if(in_array($fieldName, ['tester_id', 'designer_id', 'developer_id'])) disabled @endif
                    @endcannot>

                {{-- Permissions logic --}}
                @cannot('Set Time For Another User')
                    @if(in_array($fieldName, ['tester_id', 'designer_id', 'developer_id']))
                        <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }}</option>
                    @endif
                @endcannot

                {{-- Dynamic options --}}
                @switch($fieldName)
                    @case('new_status_id')
                        <option value="{{ $cr->getCurrentStatus()?->status?->status_name ?? '' }}" disabled selected>
                            {{ $cr->getCurrentStatus()?->status?->status_name ?? '' }}
                        </option>
                        @foreach($cr->set_status as $status)
                            <option value="{{ $status->id }}" {{ $cr->{$fieldName} == $status->id ? 'selected' : '' }}>
                                {{ $status->same_time == 1 
                                    ? $status->to_status_label 
                                    : ($status->workflowstatus[0]->to_status->high_level->name ?? $status->workflowstatus[0]->to_status->status_name) }}
                            </option>
                        @endforeach
                        @break

                    @case('release_name')
                        <option value="">Select</option>
                        @foreach($cr->get_releases() as $release)
                            <option value="{{ $release->id }}" {{ $cr->{$fieldName} == $release->id ? 'selected' : '' }}>
                                {{ $release->name }}
                            </option>
                        @endforeach
                        @break

                    @case('developer_id')
                        <option value="">Select</option>
                        @foreach($developer_users as $dev)
                            <option value="{{ $dev->id }}" {{ old($fieldName, $cr->{$fieldName} ?? '') == $dev->id ? 'selected' : '' }}>
                                {{ $dev->user_name }}
                            </option>
                        @endforeach
                        @break

                    @case('tester_id')
                        <option value="">Select</option>
                        @foreach($testing_users as $tester)
                            <option value="{{ $tester->id }}" {{ old($fieldName, $cr->{$fieldName} ?? '') == $tester->id ? 'selected' : '' }}>
                                {{ $tester->user_name }}
                            </option>
                        @endforeach
                        @break

                    @case('sa_users')
                        <option value="">Select</option>
                        @foreach($sa_users as $sa)
                            <option value="{{ $sa->id }}" {{ old($fieldName, $cr->{$fieldName} ?? '') == $sa->id ? 'selected' : '' }}>
                                {{ $sa->user_name }}
                            </option>
                        @endforeach
                        @break

                    @case('cap_users')
                        @php
                            // Retrieve selected cap_users through cab_crs relationship
                            $selectedCapUsers = [];

                            if (isset($cr)) {
                                // Get selected users through cab_crs -> cab_cr_users relationship
                                $cabCrs = $cr->cabCrs; // or $cr->cab_crs
                                foreach ($cabCrs as $cabCr) {
                                    $cabUsers = \Illuminate\Support\Facades\DB::table('cab_cr_users')
                                        ->where('cab_cr_id', $cabCr->id)
                                        ->pluck('user_id')
                                        ->toArray();
                                    $selectedCapUsers = array_merge($selectedCapUsers, $cabUsers);
                                }
                                $selectedCapUsers = array_unique($selectedCapUsers);
                            }
                            
                            // Also check for old input (form validation errors)
                            $oldValues = old($fieldName);
                            if ($oldValues) {
                                $selectedCapUsers = is_array($oldValues) ? $oldValues : [$oldValues];
                            }
                        @endphp
                        <option value="">Select</option>
                        @if(!in_array($cr->requester->id ?? '', $cap_users->pluck('user_id')->toArray()))
                            <option value="{{ $cr->requester->id ?? '' }}" {{ in_array($cr->requester->id ?? '', $selectedCapUsers) ? 'selected' : '' }}>{{ $cr->requester->name ?? '' }}</option>
                        @endif
                        @foreach($cap_users as $cap)
                            <option value="{{ $cap->user_id }}" {{ in_array($cap->user_id, $selectedCapUsers) ? 'selected' : '' }}>{{ $cap->user->name }}</option>
                        @endforeach
                        @break

                    @case('relevant')
    <option value="">Select</option>

    @php
        $changeRequests = \App\Models\change_request::where('active', 1)
            ->where('id', '!=', $cr->id ?? null) // Exclude current CR
            ->orderBy('cr_no', 'desc')
            ->get(['id', 'cr_no', 'title']);

        // Retrieve selected values from custom fields relationship
        $selectedRelevant = [];

        if (isset($cr)) {
            // Find the custom field entry for 'relevant'
            $relevantField = $cr->change_request_custom_fields
                ->where('custom_field_name', 'relevant')
                ->first();
            
            if ($relevantField && !empty($relevantField->custom_field_value)) {
                // Decode the JSON string
                $decoded = json_decode($relevantField->custom_field_value, true);
                
                if (is_array($decoded)) {
                    // Convert string IDs to integers
                    $selectedRelevant = array_map('intval', $decoded);
                }
            }
        }
        
        // Also check for old input (form validation errors)
        $oldValues = old($fieldName);
        if ($oldValues) {
            $selectedRelevant = is_array($oldValues) ? array_map('intval', $oldValues) : [$oldValues];
        }
    @endphp

    @foreach($changeRequests as $crItem)
        <option value="{{ $crItem->id }}" 
            {{ in_array((int)$crItem->id, $selectedRelevant) ? 'selected' : '' }}>
            {{ $crItem->cr_no }} - {{ $crItem->title }}
        </option>
    @endforeach
@break

                    @case('depend_on')
                        <option value="">Select CRs to depend on</option>

                        @php
                            $dependableCrsList = $dependableCrs ?? collect();

                            // Get selected dependencies from cr_dependencies table via model
                            $selectedDependencies = [];
                            if (isset($cr) && $cr->id) {
                                $selectedDependencies = $cr->getBlockingCrNumbers();
                            }
                            
                            // check for old input (form validation errors)
                            $oldDependOnValues = old('depend_on');
                            if ($oldDependOnValues) {
                                $selectedDependencies = is_array($oldDependOnValues) 
                                    ? array_map('intval', $oldDependOnValues) 
                                    : [$oldDependOnValues];
                            }
                        @endphp

                        @foreach($dependableCrsList as $dependCr)
                            <option value="{{ $dependCr->cr_no }}" 
                                {{ in_array((int)$dependCr->cr_no, $selectedDependencies) ? 'selected' : '' }}>
                                CR#{{ $dependCr->cr_no }} - {{ $dependCr->title }}
                            </option>
                        @endforeach
                        @break

                    @case('technical_teams')
                        @if(count($selected_technical_teams) > 0)
                            @php
                                $selected_teams_ids = array_column($selected_technical_teams,'id');
                            @endphp
                            @if($isEnabled)
                                @if($status_name == "Rollback" OR $status_name == "Pending Rollback" OR $status_name == "Pending fixation on production")
                                    <option value="">Select...</option>
                                    @foreach($technical_teams as $team)
                                        <option value="{{ $team['id'] }}" {{ in_array($team['id'],$selected_teams_ids) ? "selected" : "" }}>{{ $team['title'] }}</option>
                                    @endforeach
                                @else
                                    <option value="">Select...</option>
                                    @foreach($technical_teams as $team)
                                        <option value="{{ $team['id'] }}" {{ in_array($team['id'],$selected_teams_ids) ? "selected" : "" }}>{{ $team['title'] }}</option>
                                    @endforeach
                                @endif
                            @else
                                <option value="">Select...</option>
                                @foreach($technical_teams as $team)
                                    <option value="{{ $team['id'] }}" {{ in_array($team['id'],$selected_teams_ids) ? "selected" : "" }}>{{ $team['title'] }}</option>
                                @endforeach
                            @endif
                        @else
                            <option value="">Select...</option>
                            @foreach($technical_teams as $team)
                                <option value="{{ $team->id }}">{{ $team->title }}</option>
                            @endforeach
                        @endif
                        @break

                    @default
                        @if(isset($customOptions) && count($customOptions))
                            <option value="">Select</option>
                            @foreach($customOptions as $option)
                                <option value="{{ $option->id }}" {{ old($fieldName, $cr->{$fieldName} ?? '') == $option->id ? 'selected' : '' }}>
                                    {{ $option->name }}
                                </option>
                            @endforeach
                        @endif
                @endswitch
            </select>

            {{-- Hidden marker for depend_on field to allow empty submissions --}}
            @if($fieldName === 'depend_on')
                <input type="hidden" name="depend_on_exists" value="1">
            @endif

            {{-- Hidden inputs to preserve POST data when disabled --}}
            @if(!$isEnabled && count($selected_technical_teams) > 0)
                @foreach($selected_technical_teams as $team)
                    {{-- <input type="hidden" name="technical_teams[]" value="{{ $team['id'] }}"> --}}
                @endforeach
            @endif
        @endif
    </div>
@endif

{{-- IMPORTANT: Only add this once in your main layout file, NOT in this component --}}
@once
    @push('css')
        <style>
            html, body { overflow-x: hidden; }
            .select2-container { max-width: 100%; }
            .select2-dropdown { max-width: 100vw; overflow-x: hidden; }
        </style>
    @endpush

    @push('script')
        <script>
            $(function() {
                if ($.fn.select2) {
                    // Initialize all select2 fields
                    $('.select2-field').each(function() {
                        var $el = $(this);
                        
                        // Find the closest form as parent
                        var $form = $el.closest('form');
                        var dropdownParent = $form.length ? $form : $(document.body);
                        
                        $el.select2({
                            placeholder: $el.data('placeholder') || 'Select',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: dropdownParent
                        });
                    });
                }
            });
        </script>
    @endpush
@endonce