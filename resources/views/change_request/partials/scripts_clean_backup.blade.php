@push('script')

    <script src="{{asset('public/new_theme/sweetalert2@11.js')}}"></script>
    <script>
        /**
         * Change Request Form Scripts
         */

        (function ($) {
            'use strict';

            // =========================================================================
            // Configuration & Constants
            // =========================================================================
            const CONFIG = {
                pendingProductionId: "{{ $pendingProductionId }}",
                relevantNotPending: parseInt("{{ $relevantNotPending }}") || 0,
                needTechnicalAttachmentsStatuses: {!! json_encode(array_values(config('change_request.need_technical_attachments_statuses'))) !!},
                currentStatusId: "{{ isset($cr) ? $cr->current_status->new_status_id : '' }}",
                currentNeedDesignValue: "{{ isset($cr) ? optional($cr->change_request_custom_fields->where('custom_field_name', 'need_design')->first())->custom_field_value : 'null' }}",
                reminderPromoTechTeams: "{{ $reminder_promo_tech_teams_text ?? '' }}",
                scriptConfig: {!! json_encode(config('change_request_scripts')) !!}
            };

            const DOM = {
                statusSelect: 'select[name="new_status_id"]',
                crTypeSelect: 'select[name="cr_type"]',
                capUsersSelect: 'select[name="cap_users[]"]',
                capUsersWrapper: '.field_cap_users',
                rejectionReasonSelect: 'select[name="rejection_reason_id"]',
                rejectionReasonWrapper: '.field_rejection_reason_id',
                technicalAttachments: 'input[name="technical_attachments[]"]',
                workloadInput: '.field_cr_workload input',
                technicalTeamsSelect: 'select[name="technical_teams[]"]',
                technicalTeamsWrapper: '.field_technical_teams',
                dependOnSelect: 'select[name="depend_on[]"]',
                dependOnWrapper: '.field_depend_on',
                relevantSelect: 'select[name="relevant[]"]',
                relevantWrapper: '.field_relevant',
                designerSelect: 'select[name="designer_id"]',
                designEstimationInput: 'input[name="design_estimation"]',
                needDesignCheckbox: 'input[name="need_design"]',
                testingEstimationInput: 'input[name="testing_estimation"]',
                testableFlagInput: 'input[name="testable_flag"]'
            };

            // =========================================================================
            // Initialization
            // =========================================================================
            $(document).ready(function () {
                initGlobalVariables();
                initStartDatePicker();
                initModalHandlers();
                initSelect2();
                initStatusChangeHandlers();
                initCrTypeHandlers();
                initTestingEstimationHandler();
                initFormSubmissionHandlers();

                // Initial trigger to set correct state on page load
                triggerAllStatusChecks();
                triggerCrTypeCheck();
            });

            function initGlobalVariables() {
                window.pendingProductionId = CONFIG.pendingProductionId;
                window.relevantNotPending = CONFIG.relevantNotPending;
            }

            // =========================================================================
            // Logic Modules
            // =========================================================================

            /**
             * Initialize Minimum Date for Start Date Picker
             */
            function initStartDatePicker() {
                const input = document.getElementById('start_date_mds');
                if (input) {
                    const now = new Date();
                    now.setSeconds(0, 0);
                    input.min = now.toISOString().slice(0, 16);
                }
            }

            /**
             * Initialize Bootstrap Modal Handlers
             */
            function initModalHandlers() {
                $('#openModal').on('click', function () {
                    $('#modal').modal('show');
                });

                $('#close_logs').on('click', function () {
                    $('#modal').modal('hide');
                });
            }

            /**
             * Initialize Select2 Components
             */
            function initSelect2() {
                const init = () => {
                    $('.kt-select2').select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: '100%'
                    });
                };
                init();
                $(document).ajaxComplete(init);
            }

            /**
             * Central Handler for Status Changes
             */
            function initStatusChangeHandlers() {
                const $statusSelect = $(DOM.statusSelect);
                if (!$statusSelect.length) return;

                // Restrict editing on certain statuses
                const currentStatusVal = $statusSelect.find('option:selected').val();
                const lockedStatuses = CONFIG.scriptConfig.locked_statuses || [];
                if (lockedStatuses.includes(currentStatusVal)) {
                    $('input, select, textarea').not(DOM.statusSelect).prop('disabled', true);
                }

                // Store initial value for comparison later
                $statusSelect.data('initial-status', $statusSelect.val());

                // Attach Change Event
                $statusSelect.on('change', function () {
                    triggerAllStatusChecks();
                });
            }

            /**
             * Execute all status-dependent logic
             */
            function triggerAllStatusChecks() {
                const $statusSelect = $(DOM.statusSelect);
                if (!$statusSelect.length) return;

                const selectedOption = $statusSelect.find('option:selected');
                const selectedText = selectedOption.text().trim();
                const selectedValue = selectedOption.val();

                handleRejectionReason(selectedText);
                handleCapUsers(selectedText);
                handleTechnicalAttachments(selectedText);
                handleWorkload(selectedText);
                handlePromoTechnicalTeams(selectedText, selectedValue);
                handleDesignerRequirements(selectedText);
                handleTechnicalTeamsRequirements(selectedText, selectedValue);
            }

            /**
             * Logic for Rejection Reason Visibility
             */
            function handleRejectionReason(statusText) {
                const isReject = (CONFIG.scriptConfig.rejection_reason_statuses || []).includes(statusText);
                toggleFieldVisibility(DOM.rejectionReasonWrapper, DOM.rejectionReasonSelect, isReject, true);
            }

            /**
             * Logic for Cap Users Visibility
             */
            function handleCapUsers(statusText) {
                const showCapUsers = (CONFIG.scriptConfig.cap_users.show || []).some(s => statusText.includes(s));
                const hideCapUsers = (CONFIG.scriptConfig.cap_users.hide || []).some(s => statusText.includes(s));

                if (showCapUsers) {
                    toggleFieldVisibility(DOM.capUsersWrapper, DOM.capUsersSelect, true, true);
                    // Force display for stubborn CSS
                    $(DOM.capUsersWrapper).css('display', 'block').removeClass('hidden');
                } else if (hideCapUsers) {
                    toggleFieldVisibility(DOM.capUsersWrapper, DOM.capUsersSelect, false, false);
                    $(DOM.capUsersWrapper).css('display', 'none').addClass('hidden');
                } else {
                    // Default hide for others
                    toggleFieldVisibility(DOM.capUsersWrapper, DOM.capUsersSelect, false, false);
                }
            }

            /**
             * Logic for Technical Attachments
             */
            function handleTechnicalAttachments(statusText) {
                const isRequired = CONFIG.needTechnicalAttachmentsStatuses.includes(statusText);
                toggleRequirementOnly(DOM.technicalAttachments, isRequired);
            }

            /**
             * Logic for Workload Validation
             */
            function handleWorkload(statusText) {
                if ((CONFIG.scriptConfig.workload.mandatory || []).includes(statusText)) {
                    toggleRequirementOnly(DOM.workloadInput, true);
                } else if ((CONFIG.scriptConfig.workload.optional || []).includes(statusText)) {
                    toggleRequirementOnly(DOM.workloadInput, false);
                }
            }

            /**
             * Logic for Promo Technical Teams
             */
            function handlePromoTechnicalTeams(statusText, statusValue) {
                const $techTeams = $(DOM.technicalTeamsSelect);
                const $needDesign = $(DOM.needDesignCheckbox);
                // 141 = SA FB, 100 = Review CD
                const saFbId = CONFIG.scriptConfig.promo.tech_teams_mandatory_status_id; 
                const reviewCdId = CONFIG.scriptConfig.promo.review_cd_id;
                const saFbText = CONFIG.scriptConfig.promo.tech_teams_mandatory_status;
                
                if (CONFIG.currentStatusId == saFbId) {
                    if (CONFIG.currentNeedDesignValue != 'null') {
                        // Logic from original file seemed incomplete here, preserving as-is behavior
                    } else {
                        $techTeams.prop('disabled', false);
                        toggleRequirementOnly(DOM.technicalTeamsSelect, true);
                    }
                } else if (CONFIG.currentStatusId == reviewCdId || statusText === saFbText) {
                    const isRequired = (statusText === saFbText && $needDesign.is(':checked'));
                    toggleRequirementOnly(DOM.technicalTeamsSelect, isRequired);
                }

                // Attach listener for checkbox specific to this logic
                $needDesign.off('change.promo').on('change.promo', function () {
                    const currentText = $(DOM.statusSelect).find('option:selected').text().trim();
                    const saFbText = CONFIG.scriptConfig.promo.tech_teams_mandatory_status;
                    if (currentText === saFbText) {
                        toggleRequirementOnly(DOM.technicalTeamsSelect, $(this).is(':checked'));
                    }
                });
            }

            /**
            * Logic for Technical Teams General Requirements
            */
            function handleTechnicalTeamsRequirements(statusText, statusValue) {
                const hideStatuses = CONFIG.scriptConfig.technical_teams.hide_statuses || [];
                const requiredStatuses = CONFIG.scriptConfig.technical_teams.required_statuses || [];
                const hideTexts = CONFIG.scriptConfig.technical_teams.hide_texts || [];
                const extraRequiredTexts = CONFIG.scriptConfig.technical_teams.extra_required_texts || [];

                const $wrapper = $(DOM.technicalTeamsWrapper).closest('.change-request-form-field'); // Ensure we get the parent container

                let shouldHide = hideStatuses.includes(statusValue) || hideTexts.includes(statusText);
                let shouldRequire = requiredStatuses.includes(statusValue) || extraRequiredTexts.includes(statusText);

                if (shouldHide) {
                    $wrapper.hide();
                    toggleRequirementOnly(DOM.technicalTeamsSelect, false);
                } else {
                    $wrapper.show();
                    toggleRequirementOnly(DOM.technicalTeamsSelect, shouldRequire);
                }
            }

            /**
             * Logic for Designer Fields
             */
            function handleDesignerRequirements(statusText) {
                const isPendingDesign = (CONFIG.scriptConfig.designer_required_statuses || []).includes(statusText);
                toggleRequirementOnly(DOM.designerSelect, isPendingDesign);
                toggleRequirementOnly(DOM.designEstimationInput, isPendingDesign);

                // Custom asterisk handling for labels
                const labels = ['Responsible Designer', 'Design Estimation'];
                labels.forEach(labelText => {
                    const $label = $(`label:contains("${labelText}")`);
                    if (isPendingDesign) {
                        if (!$label.find('.required-mark').length) {
                            $label.prepend('<span class="required-mark" style="color: red;">* </span>');
                        }
                    } else {
                        $label.find('.required-mark').remove();
                    }
                });
            }

            /**
             * CR Type Handler (Depend On vs Relevant)
             */
            function initCrTypeHandlers() {
                const $crType = $(DOM.crTypeSelect);
                if (!$crType.length) return;

                function checkCrType() {
                    const type = $crType.find('option:selected').text().trim();

                    if (type === 'Depend On') {
                        toggleFieldVisibility(DOM.dependOnWrapper, DOM.dependOnSelect, true, true);
                        toggleFieldVisibility(DOM.relevantWrapper, DOM.relevantSelect, false, false);
                        clearField(DOM.relevantSelect);
                    } else if (type === 'Relevant') {
                        toggleFieldVisibility(DOM.dependOnWrapper, DOM.dependOnSelect, false, false);
                        toggleFieldVisibility(DOM.relevantWrapper, DOM.relevantSelect, true, true);
                        clearField(DOM.dependOnSelect);
                    } else {
                        toggleFieldVisibility(DOM.dependOnWrapper, DOM.dependOnSelect, false, false);
                        toggleFieldVisibility(DOM.relevantWrapper, DOM.relevantSelect, false, false);
                        clearField(DOM.dependOnSelect);
                        clearField(DOM.relevantSelect);
                    }
                }

                $crType.on('change', checkCrType);
                window.triggerCrTypeCheck = checkCrType; // Expose for initial call
            }

            /**
             * Testing Estimation Handler (Hidden Flag Logic)
             */
            function initTestingEstimationHandler() {
                const $input = $(DOM.testingEstimationInput);
                const $flag = $(DOM.testableFlagInput);
                const $statusSelect = $(DOM.statusSelect);

                function updateState() {
                    let flagVal = $flag.val();
                    if (!flagVal || flagVal === '') { flagVal = '0'; $flag.val('0'); }

                    const isTestable = (flagVal === '1');
                    const statusText = $statusSelect.find('option:selected').text().trim();
                    const testingEstimationText = CONFIG.scriptConfig.testing_estimation.status_text;

                    if (isTestable && statusText === testingEstimationText) {
                        $input.prop('disabled', false)
                            .removeClass('disabled bg-gray-100').addClass('bg-white')
                            .attr('placeholder', 'Enter testing estimation (must be > 0)');
                        $('label[for="testing_estimation"]').removeClass('text-gray-400').addClass('text-gray-700');
                    } else {
                        $input.prop('disabled', true)
                            .addClass('disabled bg-gray-100').removeClass('bg-white')
                            .val('0')
                            .attr('placeholder', 'Testing not required');
                        $('label[for="testing_estimation"]').addClass('text-gray-400').removeClass('text-gray-700');
                        clearValidationError($input);
                    }
                }

                $flag.on('change input', updateState);
                $statusSelect.on('change', updateState);
                updateState(); // Initial call
            }

            /**
             * Form Submission & Validation Handlers
             */
            function initFormSubmissionHandlers() {
                const $form = $('form').first(); // Adjust selector if multiple forms exist
                if (!$form.length) return;

                // Warning Loop for Promo Tech Teams
                $("#show_error_message").click(function () {
                    if (CONFIG.reminderPromoTechTeams) {
                        Swal.fire('Warning...', `There are group(s) (${CONFIG.reminderPromoTechTeams}) still not transfer CR to Smoke test yet!`, 'error');
                    }
                });

                $form.on('submit', function (e) {
                    const $statusSelect = $(DOM.statusSelect);
                    const selectedStatus = $statusSelect.val();
                    const selectedOption = $statusSelect.find('option:selected');
                    const selectedText = selectedOption.text().trim();
                    const defectValue = selectedOption.data('defect') || "0";
                    const originalValue = $statusSelect.data('original-value') || $statusSelect.val();

                    // 1. Validation for Testing Estimation
                    const $testingEstInput = $(DOM.testingEstimationInput);
                    if (!$testingEstInput.prop('disabled')) {
                        const val = parseFloat($testingEstInput.val());
                        if (isNaN(val) || val <= 0) {
                            e.preventDefault();
                            showValidationError($testingEstInput, 'Testing estimation must be greater than 0 when testable is enabled');
                            $testingEstInput.focus();
                            return false;
                        }
                    }

                    // 2. Defect Warning (Capture Phase simulation via SweetAlert pre-check)
                    const defectTargetText = CONFIG.scriptConfig.defects?.final_uat_text;
                    if (defectValue == "1" && selectedText === defectTargetText) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "There are defects related to this CRS. Are you sure you want to continue?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, continue!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Temporarily bypass this check to submit
                                selectedOption.data('defect', "0");
                                $form.submit();
                            }
                        });
                        return false;
                    }

                    // 3. Relevant CRs Not Ready Warning
                    const initialStatus = $statusSelect.data('initial-status');

                    if (
                        selectedStatus != CONFIG.pendingProductionId &&
                        initialStatus == CONFIG.pendingProductionId &&
                        CONFIG.relevantNotPending > 0
                    ) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Relevant CRs Not Ready',
                            text: "Some relevant CRs are NOT in Pending Production Deployment. Continue anyway?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, continue',
                            cancelButtonText: 'Cancel'
                        }).then(result => {
                            if (result.isConfirmed) {
                                // Temporarily bypass checks or just submit form directly from DOM element to skip jQuery handler if needed, 
                                // but here we can just set a flag or unbind. 
                                // Simplest is to remove the handler and submit, or just submit the native form.
                                $form[0].submit();
                            }
                        });
                        return false;
                    }
                });
            }

            // =========================================================================
            // Utility Functions
            // =========================================================================

            function toggleFieldVisibility(wrapperSelector, fieldSelector, shouldShow, isRequired) {
                const $wrapper = $(wrapperSelector);
                const $field = $(fieldSelector);

                if (shouldShow) {
                    $wrapper.show();
                    if (isRequired) {
                        $field.prop('required', true);
                        addAsterisk($wrapper);
                    }
                } else {
                    $wrapper.hide();
                    $field.prop('required', false);
                    removeAsterisk($wrapper);
                }
            }

            function toggleRequirementOnly(fieldSelector, isRequired) {
                const $field = $(fieldSelector);
                const $wrapper = $field.closest('.change-request-form-field');

                $field.prop('required', isRequired);
                if (isRequired) {
                    addAsterisk($wrapper);
                } else {
                    removeAsterisk($wrapper);
                }
            }

            function addAsterisk($wrapper) {
                const $label = $wrapper.find('label');
                if ($label.length && !$label.find('.required-mark').length) {
                    $label.append('<span class="required-mark" style="color: red;"> *</span>');
                }
            }

            function removeAsterisk($wrapper) {
                $wrapper.find('.required-mark').remove();
            }

            function clearField(selector) {
                const $field = $(selector);
                if ($field.is('select[multiple]')) {
                    $field.val(null).trigger('change');
                } else {
                    $field.val('').trigger('change');
                }
            }

            function showValidationError($input, message) {
                clearValidationError($input);
                $input.addClass('border-red-500 focus:border-red-500').removeClass('border-gray-300');
                $input.after(`<div class="text-red-500 text-sm mt-1 validation-error">${message}</div>`);
            }

            function clearValidationError($input) {
                $input.removeClass('border-red-500 focus:border-red-500').addClass('border-gray-300');
                $input.next('.validation-error').remove();
            }

            // Rejection Reason script (Blade Conditional)
            @if(isset($cr))
                $(DOM.statusSelect).on('change', function () {
                    const statusText = $(this).find('option:selected').text().trim();
                    const $reasonWrapper = $('#reason-wrapper');

                    if (statusText.toLowerCase() === "reject") {
                        if (!$('select[name="reason"]').length) {
                            $reasonWrapper.append('<select name="reason" class="form-control mt-2"></select>');
                            // Note: Original script just created an empty select? 
                            // Retaining original logic but this likely needs options populated.
                        }
                    } else {
                        $('select[name="reason"]').remove();
                    }
                });
            @endif

                    })(jQuery);
    </script>

    @include('change_request.partials.on_behalf_script')

@endpush
