@if ($item->CustomField->type == "input")
    @php
        $fieldName  = $item->CustomField->name;
        $fieldLabel = $item->CustomField->label;

        $validationType = $item->validation_type_id ?? null;
        $isRequired = in_array($validationType, [1, 3]); // 1 = Required, 3 = Numeric Required
        $isNumeric  = in_array($validationType, [2, 3]); // 2 or 3 => numeric type

        $isEditable = isset($item->enable) && $item->enable == 1;
        $isEstimationField = in_array($fieldName, ['dev_estimation', 'design_estimation', 'testing_estimation']);

        // Determine final HTML input type
        if ($isNumeric || $isEstimationField) {
            $fieldType = 'number';
        } elseif (in_array($fieldName, ['requester_email', 'division_manager'])) {
            $fieldType = 'email';
        } else {
            $fieldType = 'text';
        }

        // Determine input value
        if (isset($cr)) {
            $fieldValue = old($fieldName, $cr->{$fieldName} ?? ($custom_field_value ?? ''));
        } else {
            $fieldValue = old($fieldName, $custom_field_value ?? '');
        }

        if ($fieldName == 'man_days') {
            $fieldValue = "";
        }
    @endphp

    <div class="col-md-6 change-request-form-field field_{{ $fieldName }}">
        <label for="{{ $fieldName }}">{{ $fieldLabel }}</label>
        @if ($isRequired)
            <span class="text-danger">*</span>
        @endif

        @if ($isEditable)
            {{-- Special Case: Division Manager --}}
            @if ($fieldName === 'division_manager')
                <input
                    type="email"
                    id="division_manager"
                    name="{{ $fieldName }}"
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror"
                    value="{{ $fieldValue }}"
                    @if ($isRequired) required @endif
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <small id="email_feedback" class="form-text text-danger"></small>

            {{-- Requester name/email special cases --}}
            @elseif(in_array($fieldName, ['requester_name', 'requester_email']))
                <input
                    type="{{ $fieldType }}"
                    name="{{ $fieldName }}"
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror"
                    value="{{ $fieldName === 'requester_name' ? auth()->user()->name : auth()->user()->email }}"
                    readonly
                    @if ($isRequired) required @endif
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror

            {{-- Normal editable input --}}
            @else
                <input
                    type="{{ $fieldType }}"
                    name="{{ $fieldName }}"
                    class="form-control form-control-lg @error($fieldName) is-invalid @enderror"
                    value="{{ $fieldValue }}"
                    @if ($isRequired) required @endif
                    @if ($isNumeric) step="any" @endif
                    @if ($isEstimationField) placeholder="Estimation (hours)" @endif
                />
                @error($fieldName)
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            @endif

        {{-- Read-only (disabled) view --}}
        @elseif(isset($cr))
            <input
                type="{{ $fieldType }}"
                name="{{ $fieldName }}"
                class="form-control form-control-lg"
                value="{{ $fieldValue }}"
                disabled
            />
        @endif
    </div>
@endif
