@push('script')

    <script>
        // --- 1. Global Variables & Config ---
        window.pendingProductionId = "{{ $pendingProductionId }}";
        window.relevantNotPending = "{{ $relevantNotPending }}";

        // PHP Config for statuses
        const CONFIG = {
            STATUS: {
                REJECT: ['Reject', 'Reject kam', 'CR Team FB'],
                CLOSED: ['Closed', 'CR Closed', 'Closed kam'],
                PENDING_CAB: ['Pending CAB', 'Pending CAB Approval', 'CR Doc Valid'],
                PENDING_DESIGN: ['Pending Design', 'Pending Design kam'],
                PENDING_TESTING: ['Pending Testing'],
                RELEASE_PLAN: ['Release Plan'],
                PENDING_BUSINESS: ['Pending Business'],
                TEST_CASE_APPROVAL: ['Test Case Approval', 'Test Case Approved kam'],
                PROMO_VALIDATION: ['Promo Validation'],
                SA_FB: ['SA FB'],
                PENDING_CD_FB: ['Pending CD FB', "Request MD's"],
                HIDE_TECH_TEAMS: ["Test in Progress", "Pending HL Design", "Assess the defects"],
                REQUIRED_TECH_TEAMS_IDS: ["257", "220", "276", "275"], // IDs from legacy logic
            },
            TECH_ATTACHMENTS_REQUIRED: {!! json_encode(array_values(config('change_request.need_technical_attachments_statuses', []))) !!}
        };

        // --- 2. Helper Functions ---
        const UI = {
            // Toggles visibility and required state of a field wrapper
            toggleField: function (wrapperSelector, show, required = false) {
                const $wrapper = $(wrapperSelector);
                if (!$wrapper.length) return;

                const $inputs = $wrapper.find('input, select, textarea');
                const $label = $wrapper.find('label');

                if (show) {
                    $wrapper.show().removeClass('hidden').css('display', ''); // Force show
                    this.toggleRequired($wrapper, required);
                } else {
                    $wrapper.hide().addClass('hidden').css('display', 'none'); // Force hide
                    $inputs.prop('required', false).removeAttr('required');
                    this.removeAsterisk($label);
                }
            },

            // Toggles only the required state of a field
            toggleRequired: function (wrapperSelector, required) {
                const $wrapper = $(wrapperSelector);
                if (!$wrapper.length) {
                    // Fallback: try finding by input inside if wrapper selector is actually an input wrapper
                    // But we assume wrapperSelector finds the container
                    return;
                }

                const $inputs = $wrapper.find('input, select, textarea');
                const $label = $wrapper.find('label');

                if (required) {
                    $inputs.prop('required', true).attr('required', 'required');
                    this.addAsterisk($label);
                } else {
                    $inputs.prop('required', false).removeAttr('required');
                    this.removeAsterisk($label);
                }
            },

            addAsterisk: function ($label) {
                if ($label.length && !$label.find('.required-mark').length) {
                    // check if it already has a raw * text
                    if (!$label.text().includes('*')) {
                        $label.append('<span class="required-mark" style="color: red;"> *</span>');
                    }
                }
            },

            removeAsterisk: function ($label) {
                if ($label.length) {
                    $label.find('.required-mark').remove();
                    // Cleanup legacy asterisks if any
                    // $label.html($label.html().replace(/\s*<span[^>]*>\*<\/span>/g, '').replace(/\s*\*/g, ''));
                }
            },

            clearField: function ($field) {
                if (!$field.length) return;
                if ($field.is('select[multiple]')) {
                    $field.find('option').prop('selected', false);
                } else {
                    $field.val('');
                }
                $field.trigger('change');
            }
        };

        $(document).ready(function () {
            // --- 3. DOM Elements ---
            const $statusSelect = $('select[name="new_status_id"]');
            const $crTypeSelect = $('select[name="cr_type"]');
            const $needDesignCheckbox = $('input[name="need_design"]');

            // Store original value for submit check
            $statusSelect.data('original-value', $statusSelect.val());

            // --- 4. Main Event Handlers ---

            function handleStatusChange() {
                const statusText = $statusSelect.find('option:selected').text().trim();
                const statusVal = $statusSelect.val();

                // 4.1 Global Disable for Closed/Reject
                if (CONFIG.STATUS.REJECT.includes(statusText) || CONFIG.STATUS.CLOSED.includes(statusText)) {
                    $('input, select, textarea').not('#new_status_id').prop('disabled', true);
                }

                // 4.2 Cap Users
                const showCapUsers = CONFIG.STATUS.PENDING_CAB.some(s => statusText.includes(s));
                UI.toggleField('.field_cap_users', showCapUsers, showCapUsers);
                // Force display style for stubbornly hidden elements
                if (showCapUsers) {
                    $('.field_cap_users').css('display', 'block');
                    $('select[name="cap_users[]"]').prop('required', true);
                } else {
                    $('.field_cap_users').css('display', 'none !important');
                }

                // 4.3 Rejection Reason
                const showRejectReason = CONFIG.STATUS.REJECT.includes(statusText);
                UI.toggleField('.field_rejection_reason_id', showRejectReason, showRejectReason);

                // 4.4 Technical Attachments
                const showTechAttachments = CONFIG.TECH_ATTACHMENTS_REQUIRED.includes(statusText);
                const $techAttachWrapper = $('input[name="technical_attachments[]"]').closest('.change-request-form-field');
                UI.toggleRequired($techAttachWrapper, showTechAttachments);

                if (CONFIG.STATUS.TEST_CASE_APPROVAL.includes(statusText)) {
                    UI.toggleRequired($techAttachWrapper, true);
                }

                // 4.5 Workload
                const $workloadWrapper = $('.field_cr_workload');
                if (CONFIG.STATUS.RELEASE_PLAN.includes(statusText)) {
                    UI.toggleRequired($workloadWrapper, true);
                } else if (CONFIG.STATUS.PENDING_BUSINESS.includes(statusText)) {
                    UI.toggleRequired($workloadWrapper, false);
                }

                // 4.6 Responsible Designer & Design Estimation
                const isPendingDesign = CONFIG.STATUS.PENDING_DESIGN.includes(statusText);
                const $designerWrapper = $('select[name="designer_id"]').closest('.change-request-form-field');
                const $designEstWrapper = $('input[name="design_estimation"]').closest('.change-request-form-field');

                // Special handling for label HTML injection (as per original script preference)
                const $designerLabel = $designerWrapper.find('label');
                const $estLabel = $designEstWrapper.find('label');

                if (isPendingDesign) {
                    $('select[name="designer_id"]').prop('required', true);
                    $('input[name="design_estimation"]').prop('required', true);
                    if ($designerLabel.length && !$designerLabel.html().includes('*')) {
                        $designerLabel.prepend('<span style="color: red;">*</span> ');
                    }
                    if ($estLabel.length && !$estLabel.html().includes('*')) {
                        $estLabel.prepend('<span style="color: red;">*</span> ');
                    }
                } else {
                    $('select[name="designer_id"]').prop('required', false);
                    $('input[name="design_estimation"]').prop('required', false);
                    if ($designerLabel.length) $designerLabel.html($designerLabel.html().replace(/<span style="color: red;">\*<\/span> /, ""));
                    if ($estLabel.length) $estLabel.html($estLabel.html().replace(/<span style="color: red;">\*<\/span> /, ""));
                }

                // 4.7 Technical Teams
                const $techTeamWrapper = $('.field_technical_teams');
                // Logic based on original script lines 1020-1043 & 406-446

                // Check if hidden by ID or Text
                const shouldHide = CONFIG.STATUS.HIDE_TECH_TEAMS.includes(statusText) || CONFIG.STATUS.HIDE_TECH_TEAMS.includes(statusVal);

                if (shouldHide) {
                    UI.toggleField($techTeamWrapper, false);
                } else {
                    $techTeamWrapper.show().removeClass('hidden').css('display', '');

                    let techRequired = false;
                    // Status-based requirements from lines 1021
                    if (CONFIG.STATUS.REQUIRED_TECH_TEAMS_IDS.includes(statusVal)) {
                        techRequired = true;
                    }
                    // SA FB + Need Design
                    if (CONFIG.STATUS.SA_FB.includes(statusText) && $needDesignCheckbox.is(':checked')) {
                        techRequired = true;
                    }
                    // Pending CD FB / Request MD's
                    if (CONFIG.STATUS.PENDING_CD_FB.includes(statusText)) {
                        techRequired = true;
                    }

                    UI.toggleRequired($techTeamWrapper, techRequired);
                }
            }

            function handleCrTypeChange() {
                const typeText = $crTypeSelect.find('option:selected').text().trim();
                const $dependOnWrapper = $('.field_depend_on');
                const $dependOnSelect = $('select[name="depend_on[]"]');
                const $relevantWrapper = $('.field_relevant');
                const $relevantSelect = $('select[name="relevant[]"]');

                if (typeText === 'Depend On') {
                    UI.toggleField($dependOnWrapper, true, true);
                    UI.toggleField($relevantWrapper, false);
                    UI.clearField($relevantSelect);
                } else if (typeText === 'Relevant') {
                    UI.toggleField($dependOnWrapper, false);
                    UI.clearField($dependOnSelect);
                    UI.toggleField($relevantWrapper, true, true);
                } else {
                    UI.toggleField($dependOnWrapper, false);
                    UI.clearField($dependOnSelect);
                    UI.toggleField($relevantWrapper, false);
                    UI.clearField($relevantSelect);
                }
            }

            // --- 5. Event Listeners ---
            $statusSelect.on('change', handleStatusChange);
            $crTypeSelect.on('change', handleCrTypeChange);
            $needDesignCheckbox.on('change', handleStatusChange);

            // --- 6. Initial Methods ---

            // Start Date MDS Logic
            const mdsInput = document.getElementById('start_date_mds');
            if (mdsInput) {
                const now = new Date();
                now.setSeconds(0, 0);
                mdsInput.min = now.toISOString().slice(0, 16);
            }

            // Bootstrap Modal Logic
            $('#openModal').on('click', function () { $('#modal').modal('show'); });
            $('#close_logs').on('click', function () { $('#modal').modal('hide'); });

            // Show Error Message Click
            $("#show_error_message").click(function () {
                const message = " There are group(s) ({{$reminder_promo_tech_teams_text}}) still not transfer CR to Smoke test yet!"
                Swal.fire('Warning...', message, 'error')
            });

            // Select2 Init
            function initSelect2() {
                $('.kt-select2').select2({ placeholder: "Select options", allowClear: true, width: '100%' });
            }
            initSelect2();
            $(document).ajaxComplete(initSelect2);

            // Initial Run
            handleStatusChange();
            handleCrTypeChange();

            // Periodic Check (Safety Net)
            let checkCount = 0;
            const checkInterval = setInterval(function () {
                checkCount++;
                handleStatusChange();
                if (checkCount >= 5) clearInterval(checkInterval);
            }, 500);
        });

        // --- 7. Testable Flag Logic ---
        $(document).ready(function () {
            const $testingEstimationInput = $('input[name="testing_estimation"]');
            const $testableFlagInput = $('input[name="testable_flag"]');
            const $statusSelect = $('select[name="new_status_id"]');

            if (!$testingEstimationInput.length || !$testableFlagInput.length) return;

            function updateEstimationFieldState() {
                let flagValue = $testableFlagInput.val();
                if (!flagValue || flagValue.trim() === '') {
                    flagValue = '0';
                    $testableFlagInput.val('0');
                }

                const isTestable = flagValue === '1';
                const statusText = $statusSelect.find('option:selected').text().trim();

                if (isTestable && statusText.includes('Testing Estimation')) {
                    $testingEstimationInput.prop('disabled', false)
                        .removeClass('disabled bg-gray-100').addClass('bg-white')
                        .attr('placeholder', 'Enter testing estimation (must be > 0)');
                    $('label[for="testing_estimation"]').removeClass('text-gray-400').addClass('text-gray-700');
                } else {
                    $testingEstimationInput.prop('disabled', true)
                        .addClass('disabled bg-gray-100').removeClass('bg-white')
                        .val('0').attr('placeholder', 'Testing not required');
                    $('label[for="testing_estimation"]').addClass('text-gray-400').removeClass('text-gray-700');
                }
            }

            $testableFlagInput.on('change input', updateEstimationFieldState);
            $statusSelect.on('change', updateEstimationFieldState);
            updateEstimationFieldState();

            // Expose global updater
            window.updateTestableFlag = function (value) {
                $testableFlagInput.val(value ? '1' : '0').trigger('change');
            };
        });

        // --- 8. Form Submit Interceptor ---
        document.addEventListener("DOMContentLoaded", function () {
            // Prevent double init
            if (window.checkStatusBeforeSubmitInitialized) return;
            window.checkStatusBeforeSubmitInitialized = true;

            const statusSelect = document.querySelector('select[name="new_status_id"]');
            const form = statusSelect ? statusSelect.closest("form") : null;

            if (form) {
                form.addEventListener("submit", function (event) {
                    const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                    const selectedText = selectedOption?.textContent.trim();
                    const defectValue = selectedOption?.getAttribute('data-defect') || "0";

                    // Original Value Check for Relevant CRs
                    // We need to access the data stored in jQuery or just assume logic needs to run
                    // The original script logic was:
                    // if (status != pendingProductionId && originalValue == pendingProductionId && relevantNotPending > 0)
                    // We can access jquery data from vanilla JS via $(element).data()
                    const checkOriginalValue = $(statusSelect).data('original-value');

                    if (
                        statusSelect.value != window.pendingProductionId &&
                        checkOriginalValue == window.pendingProductionId &&
                        parseInt(window.relevantNotPending) > 0
                    ) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Relevant CRs Not Ready',
                            text: "Some relevant CRs are NOT in Pending Production Deployment. Continue anyway?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, continue',
                        }).then(result => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                        return;
                    }

                    // Defect Check
                    if (defectValue === "1" && selectedText === "Final UAT Results & FB") {
                        event.preventDefault();
                        event.stopPropagation();

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "There are defects related to this CRS. Are you sure you want to continue?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, continue!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                }, true); // Capture phase true
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endpush