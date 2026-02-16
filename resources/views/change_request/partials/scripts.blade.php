@push('script')

    <script>
        const input = document.getElementById('start_date_mds');
        if (input) {
            const now = new Date();
            now.setSeconds(0, 0); // remove seconds & milliseconds
            const minDateTime = now.toISOString().slice(0, 16);
            input.min = minDateTime;
        }
    </script>


    <script>

        window.pendingProductionId = "{{ $pendingProductionId }}";
        window.relevantNotPending = "{{ $relevantNotPending }}";

        // Modern Bootstrap Modal Handler
        var btn = document.getElementById("openModal");
        var closeBtn = document.getElementById("close_logs");

        // Open modal with Bootstrap
        if (btn) {
            btn.onclick = function () {
                $('#modal').modal('show');
            }
        }

        // Close modal with Bootstrap
        if (closeBtn) {
            closeBtn.onclick = function () {
                $('#modal').modal('hide');
            }
        }

        $(document).ready(function () {
            var status = $('select[name="new_status_id"] option:selected').val();
            if (status === "Reject" || status === "Closed" || status === "CR Closed" || status === "Reject kam" || status === "Closed kam") {
                $('input, select, textarea').prop('disabled', true);
            }
            $('#new_status_id').prop('disabled', false);
        });

    </script>

    @include('change_request.partials.on_behalf_script')

    <script>
        $(window).on("load", function () {
            // Force hide cap_users field immediately and multiple times
            //console.log('=== Page load: Force hiding cap_users field ===');
            $(".field_cap_users").hide();
            $('select[name="cap_users[]"]').prop('required', false);

            // Also hide with CSS for extra force
            $(".field_cap_users").css('display', 'none !important');
            $('.field_cap_users').attr('style', 'display: none !important');

            const statusField = document.querySelector('select[name="new_status_id"]');
            //console.log('Status field found:', !!statusField);
            if (statusField) {
                //console.log('Current status options:');
                for (let i = 0; i < statusField.options.length; i++) {
                    //console.log(`Option ${i}: "${statusField.options[i].text}" (value: ${statusField.options[i].value})`);
                }
                //console.log('Selected index:', statusField.selectedIndex);
                //console.log('Selected text:', statusField.options[statusField.selectedIndex]?.text);
            }
            // Function to check if the status is "Reject"
            function isStatusReject() {
                if (statusField) {
                    const selectedText = statusField.options[statusField.selectedIndex].text;
                    return selectedText === "Reject" || selectedText === "Reject kam" || selectedText === "CR Team FB";
                }
                return false;
            }

            function isStatusPromo() {
                if (statusField) {
                    const selectedStatusPromo = statusField.options[statusField.selectedIndex].text;
                    return selectedStatusPromo === "Promo Validation";
                }
                return false;
            }

            // Function to handle the visibility of rejection reasons field and label
            // Function to handle the visibility of rejection reasons field and label
            function handleRejectionReasonsVisibility() {
                const label = document.querySelector('.field_rejection_reason_id label');
                if (isStatusReject()) {
                    $(".field_rejection_reason_id").show();
                    $('select[name="rejection_reason_id"]').prop('required', true);

                    // Add red asterisk
                    if (label && !label.querySelector('.required-mark')) {
                        const asterisk = document.createElement('span');
                        asterisk.className = 'required-mark';
                        asterisk.style.color = 'red';
                        asterisk.textContent = ' *';
                        label.appendChild(asterisk);
                    }
                } else {
                    $(".field_rejection_reason_id").hide();
                    $('select[name="rejection_reason_id"]').prop('required', false);

                    // Remove red asterisk
                    if (label) {
                        const asterisk = label.querySelector('.required-mark');
                        if (asterisk) asterisk.remove();
                    }
                }
            }

            // Check the status on page load
            handleRejectionReasonsVisibility();

            // Add an event listener to the status field to handle change events
            if (statusField) {
                statusField.addEventListener("change", handleRejectionReasonsVisibility);
            }

            // make technical attachments required on specific statuses
            function handleTechnicalAttachmentsVisibility() {

                const technicalAttachmentField = document.querySelector('input[name="technical_attachments[]"]');
                const selectedStatus = statusField.options[statusField.selectedIndex].text.trim();
                const requiredStatuses = {!! json_encode(array_values(config('change_request.need_technical_attachments_statuses'))) !!};
                const isRequired = requiredStatuses.includes(selectedStatus);

                if (technicalAttachmentField) {
                    technicalAttachmentField.required = isRequired;
                }

                // add red asterisk if required
                if (technicalAttachmentField) {
                    const container = technicalAttachmentField.closest('.change-request-form-field');
                    if (container) {
                        const label = container.querySelector('label');
                        if (label) {
                            let asterisk = label.querySelector('.required-mark');
                            if (isRequired) {
                                if (!asterisk) {
                                    asterisk = document.createElement('span');
                                    asterisk.className = 'required-mark';
                                    asterisk.style.color = 'red';
                                    asterisk.innerHTML = ' *';
                                    label.appendChild(asterisk);
                                }
                            } else {
                                if (asterisk) {
                                    asterisk.remove();
                                }
                            }
                        }
                    }
                }
            }

            handleTechnicalAttachmentsVisibility();

            statusField.addEventListener("change", handleTechnicalAttachmentsVisibility);

            // Select Elements
            const crTypeField = document.querySelector('select[name="cr_type"]');
            const dependOnContainer = document.querySelector('.field_depend_on');
            const relevantContainer = document.querySelector('.field_relevant');
            const dependOnSelect = document.querySelector('select[name="depend_on[]"]');
            const relevantSelect = document.querySelector('select[name="relevant[]"]');

            // Helper to clear values properly 
            function clearField(field) {
                if (!field) return;

                if (field.type === 'select-multiple') {
                    Array.from(field.options).forEach(option => option.selected = false);
                } else {
                    field.value = '';
                }

                field.dispatchEvent(new Event('change', { bubbles: true }));
                if (typeof $ !== 'undefined') { $(field).trigger('change'); }
            }

            // Helper to toggle visibility and requirements
            function toggleFieldState(container, field, shouldShow) {
                if (!container) return;

                const label = container.querySelector('label');

                if (shouldShow) {
                    // Show: Use jQuery show() to ensure it overrides any CSS properly
                    $(container).show();

                    if (field) field.setAttribute('required', 'required');

                    // Add Asterisk for required fields
                    if (label && !label.querySelector('.cr-type-required-mark')) {
                        const span = document.createElement('span');
                        span.className = 'cr-type-required-mark';
                        span.style.color = 'red';
                        span.textContent = ' *';
                        label.appendChild(span);
                    }
                } else {
                    // Hide: Use jQuery hide()
                    $(container).hide();

                    if (field) field.removeAttribute('required');

                    // Remove Asterisk
                    if (label) {
                        const asterisk = label.querySelector('.cr-type-required-mark');
                        if (asterisk) asterisk.remove();
                    }

                    // CRITICAL: Clear the value when hiding
                    clearField(field);
                }
            }

            // Main Handler Function
            function handleCrTypeVisibility() {
                if (!crTypeField) return;

                const selectedOption = crTypeField.options[crTypeField.selectedIndex];
                const selectedText = selectedOption ? selectedOption.text.trim() : '';

                // Logic Switch
                if (selectedText === 'Depend On') {
                    toggleFieldState(dependOnContainer, dependOnSelect, true);  // Show Depend On
                    toggleFieldState(relevantContainer, relevantSelect, false); // Hide & Clear Relevant
                }
                else if (selectedText === 'Relevant') {
                    toggleFieldState(dependOnContainer, dependOnSelect, false); // Hide & Clear Depend On
                    toggleFieldState(relevantContainer, relevantSelect, true);  // Show Relevant
                }
                else {
                    // Normal / Empty: Hide & Clear BOTH
                    toggleFieldState(dependOnContainer, dependOnSelect, false);
                    toggleFieldState(relevantContainer, relevantSelect, false);
                }
            }


            if (crTypeField) {
                // Run immediately to handle page load / old values
                handleCrTypeVisibility();

                // Listen for user changes
                crTypeField.addEventListener('change', handleCrTypeVisibility);
            }


        });


        $(document).ready(function () {
            //console.log('=== SCRIPT IS RUNNING ===');

            //console.log('=== CAP USERS VISIBILITY CONTROL ===');

            // Multiple approaches to find the cap_users field
            //console.log('=== FIELD DETECTION DEBUG ===');

            // Approach 1: By class name
            var field1 = $('.field_cap_users');
            //console.log('Approach 1 - .field_cap_users:', field1.length, field1);

            // Approach 2: By input name
            var field2 = $('select[name="cap_users[]"]');
            //console.log('Approach 2 - select[name="cap_users[]"]:', field2.length, field2);

            // Approach 3: By containing div
            var field3 = $('div').has('select[name="cap_users[]"]');
            //console.log('Approach 3 - div containing cap_users:', field3.length, field3);

            // Approach 4: By data attributes
            var field4 = $('[data-field-name="cap_users"]');
            //console.log('Approach 4 - data-field-name="cap_users":', field4.length, field4);

            // Approach 5: By label text
            var field5 = $('label:contains("Cab Users")').closest('div');
            // console.log('Approach 5 - label containing "Cab Users":', field5.length, field5);

            // Simple and direct approach
            function updateCapUsersVisibility() {
                const statusSelect = $('select[name="new_status_id"]');
                const statusText = statusSelect.find('option:selected').text();
                //console.log('=== UPDATE CHECK ===');
                //console.log('Status select exists:', statusSelect.length > 0);
                //console.log('Current status:', '"' + statusText + '"');

                // Try to find the field again
                const capField = $('.field_cap_users');
                const capSelect = $('select[name="cap_users[]"]');
                //console.log('Field found by class:', capField.length > 0);
                //console.log('Select found by name:', capSelect.length > 0);

                if (statusText.includes('Pending CAB') || statusText.includes('Pending CAB Approval') || statusText.includes('CR Doc Valid')) {
                    //console.log('🟢 ACTION: SHOWING cap_users field');

                    // Try multiple show methods
                    $('.field_cap_users').show();
                    $('.field_cap_users').css('display', 'block');
                    $('.field_cap_users').css('visibility', 'visible');
                    $('.field_cap_users').removeClass('hidden');

                    // Also try showing the select directly
                    $('select[name="cap_users[]"]').show();
                    $('select[name="cap_users[]"]').css('display', 'block');

                    // Set required
                    $('select[name="cap_users[]"]').prop('required', true);
                    $('select[name="cap_users[]"]').attr('required', 'required');

                    //console.log('Show methods executed');
                } else {
                    //console.log('🔴 ACTION: HIDING cap_users field');

                    // Try multiple hide methods
                    $('.field_cap_users').hide();
                    $('.field_cap_users').css('display', 'none');
                    $('.field_cap_users').css('visibility', 'hidden');
                    $('.field_cap_users').addClass('hidden');

                    // Also try hiding the select directly
                    $('select[name="cap_users[]"]').hide();
                    $('select[name="cap_users[]"]').css('display', 'none');

                    // Remove required
                    $('select[name="cap_users[]"]').prop('required', false);
                    $('select[name="cap_users[]"]').removeAttr('required');

                    //console.log('Hide methods executed');
                }

                // Log current state after changes
                setTimeout(function () {
                    const finalField = $('.field_cap_users');
                    const finalSelect = $('select[name="cap_users[]"]');
                    //console.log('=== FINAL STATE ===');
                    //console.log('Field display:', finalField.css('display'));
                    //console.log('Field visibility:', finalField.css('visibility'));
                    //console.log('Select display:', finalSelect.css('display'));
                    //console.log('Select required:', finalSelect.prop('required'));
                }, 100);
            }

            // Initial check immediately (no delay)
            //console.log('=== INITIAL CHECK ===');
            updateCapUsersVisibility();

            // Check on status change
            $('select[name="new_status_id"]').on('change', function () {
                //console.log('=== STATUS CHANGE EVENT ===');
                updateCapUsersVisibility();
            });

            // Also check periodically for dynamic loading (reduced frequency and only if field is hidden)
            setInterval(function () {
                const currentStatus = $('select[name="new_status_id"]').find('option:selected').text();
                const shouldShow = currentStatus.includes('Pending CAB') || currentStatus.includes('Pending CAB Approval') || currentStatus.includes('CR Doc Valid');
                const isCurrentlyHidden = $('.field_cap_users').css('display') === 'none';

                // Only update if there's a mismatch
                if (shouldShow && isCurrentlyHidden) {
                    //console.log('🔄 PERIODIC CHECK: SHOWING field (was hidden)');
                    updateCapUsersVisibility();
                } else if (!shouldShow && !isCurrentlyHidden) {
                    //console.log('🔄 PERIODIC CHECK: HIDING field (was visible)');
                    updateCapUsersVisibility();
                }
            }, 5000);
        });

        // handle worlkload validation.. mandatory when transfer status from Analysis to Release plan and optional when transfer status from Analysis to Pending business Feedback
        // handle promo instatus "Review CD" and "SA FB"
        $(document).ready(function () {
            const statusField = $('select[name="new_status_id"]');
            const workLoadField = $(".field_cr_workload input");
            //const technicalAttachmentField = $(".field_technical_attachments input"); 
            const technicalAttachmentField = $('input[name="technical_attachments[]"]');

            //console.log("Status Field and Work Load Field Found");


            function handleWorkLoadValidation() {
                const selectedStatus = statusField.find("option:selected").text().trim();
                //console.log("Selected Status:", selectedStatus); 
                //console.log("Technical Attachment Field:", technicalAttachmentField.length ? "Found" : "Not found");


                if (selectedStatus === "Release Plan") {
                    workLoadField.prop("required", true); // mandatory
                    //console.log("Work Load is now mandatory");
                } else if (selectedStatus === "Pending Business") {
                    workLoadField.prop("required", false); // optional
                }

                if (selectedStatus === "Test Case Approval" || selectedStatus === "Test Case Approved kam") {
                    technicalAttachmentField.prop("required", true); // mandatory
                    //console.log("Technical Attachment is now mandatory");
                }
                else {
                    technicalAttachmentField.prop("required", false); // optional
                }
            }
            //$(document).on('change', 'input[name="need_design"]', handlePromoStatusValidation);

            // function to handle promo, technical teams will be mandatory when selected status is "SA FB" and "Need Design" checkbox is checked
            function handlePromoStatusValidation() {

                const selectedStatus = statusField.find("option:selected").text().trim();
                const needDesignCheckbox = $('input[name="need_design"]');
                const technicalTeamsField = $('select[name="technical_teams[]"]');
                const techLabel = $('.field_technical_teams label');

                //console.log("Selected Status:", selectedStatus);
                //console.log("Need Design Checkbox:", needDesignCheckbox.length ? "Found" : "Not found");
                //console.log("Technical Teams Field:", technicalTeamsField.length ? "Found" : "Not found");

                // Check if status is "SA FB" and need_design is checked
                if (selectedStatus === "SA FB" && needDesignCheckbox.is(':checked')) {
                    // Make technical teams required
                    technicalTeamsField.prop("required", true);

                    // Add red asterisk if not already there
                    if (techLabel.length && !techLabel.find(".required-mark").length) {
                        techLabel.append('<span class="required-mark" style="color: red;"> *</span>');
                    }

                    // Add visual styling to indicate required field
                    //technicalTeamsField.addClass('required-field');

                    //console.log("Technical Teams is now mandatory - Status: SA FB, Need Design: checked");
                } else {
                    // Remove required if conditions are not met
                    technicalTeamsField.prop("required", false);

                    // Remove the asterisk if it exists
                    if (techLabel.length) {
                        techLabel.find(".required-mark").remove();
                    }

                    // Remove visual styling
                    technicalTeamsField.removeClass('required-field');

                    //console.log("Technical Teams is now optional");
                }
            }

            // handle promo, technical teams will be disabled when need_design is checked and enabled when need_design is unchecked
            function handlePromoTechnicalTeams() {
                const currentStatus = "{{ $cr->current_status->new_status_id}}";
                const selectedStatus = statusField.find("option:selected").text().trim();
                const needDesignCheckbox = $('input[name="need_design"]');
                const technicalTeamsField = $('select[name="technical_teams[]"]');
                const techLabel = $('.field_technical_teams label');
                const needDesign = "{{ optional($cr->change_request_custom_fields->where('custom_field_name', 'need_design')->first())->custom_field_value ?? 'null' }}";

                //console.log("Current Status:", currentStatus);
                //console.log("Selected Status:", selectedStatus);
                //console.log("Need Design Checkbox:", needDesignCheckbox.length ? "Found" : "Not found");
                //console.log("Technical Teams Field:", technicalTeamsField.length ? "Found" : "Not found");
                //console.log("Need Design:", needDesign);
                // 141 = SA FB
                if (currentStatus == "141") {
                    if (needDesign != 'null') {
                        //technicalTeamsField.prop("disabled", true);
                        //console.log("Technical Teams is now disabled");
                    } else {
                        technicalTeamsField.prop("disabled", false);
                        technicalTeamsField.prop("required", true);
                        if (techLabel.length && !techLabel.find(".required-mark").length) {
                            techLabel.append('<span class="required-mark" style="color: red;"> *</span>');
                        }
                        //console.log("Technical Teams is now enabled and required");
                    }
                }

            }
            const currentStatus = "{{ $cr->current_status->new_status_id}}";
            // 141 = SA FB
            // 100 = Review CD
            if (currentStatus == "141") {
                //handlePromoTechnicalTeams();
                statusField.on("change", handlePromoTechnicalTeams);
            } else if (currentStatus == "100") {
                handlePromoStatusValidation();
                statusField.on("change", handlePromoStatusValidation);
                $(document).on('change', 'input[name="need_design"]', handlePromoStatusValidation);

            } else {
                handleWorkLoadValidation();
                statusField.on("change", handleWorkLoadValidation);

            }

            /* Also check on page load for initial state
            $(document).ready(function() {
                handlePromoStatusValidation();
            }); */



        });




        $(window).on("load", function () {
            const statusField = document.querySelector('select[name="new_status_id"]');
            const responsibleDesignerField = document.querySelector('select[name="designer_id"]'); // Assuming the field is an input field
            const responsibleDesignerLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.trim() === "Responsible Designer");
            const DesigneEstimationLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.trim() === "Design Estimation");
            const DesigneEstimationInput = document.querySelector('input[name="design_estimation"]');

            // Function to check if the status is "Pending Design"
            function isStatusPendingDesign() {
                if (statusField) {
                    const selectedText = statusField.options[statusField.selectedIndex].text;
                    return selectedText === "Pending Design" || selectedText === "Pending Design kam";
                }
                return false;
            }

            // Function to handle the field as optional or required
            function handleOptionalOrRequiredOption() {
                if (isStatusPendingDesign()) {
                    // Add "*" above the field name "Responsible Designer" and make the field required
                    if (responsibleDesignerLabel && !responsibleDesignerLabel.innerHTML.includes("*")) {
                        /*responsibleDesignerLabel.innerHTML = " * " + responsibleDesignerLabel.innerHTML;
                        DesigneEstimationLabel.innerHTML = " * " + DesigneEstimationLabel.innerHTML;*/
                        responsibleDesignerLabel.innerHTML = `<span style="color: red;">*</span> ` + responsibleDesignerLabel.innerHTML;
                        DesigneEstimationLabel.innerHTML = `<span style="color: red;">*</span> ` + DesigneEstimationLabel.innerHTML;
                    }
                    if (responsibleDesignerField) {
                        responsibleDesignerField.setAttribute("required", true);
                        DesigneEstimationInput.setAttribute("required", true);
                    }
                } else {
                    // Remove "*" above the field name "Responsible Designer" and make the field optional
                    if (responsibleDesignerLabel && responsibleDesignerLabel.innerHTML.includes("*")) {
                        /*responsibleDesignerLabel.innerHTML = responsibleDesignerLabel.innerHTML.replace("*", "");
                        DesigneEstimationLabel.innerHTML = DesigneEstimationLabel.innerHTML.replace("*", "");*/
                        responsibleDesignerLabel.innerHTML = responsibleDesignerLabel.innerHTML.replace(/<span style="color: red;">\*<\/span> /, "");
                        DesigneEstimationLabel.innerHTML = DesigneEstimationLabel.innerHTML.replace(/<span style="color: red;">\*<\/span> /, "");
                    }
                    if (responsibleDesignerField) {
                        responsibleDesignerField.removeAttribute("required");
                        DesigneEstimationInput.removeAttribute("required");
                    }
                }
            }

            // Check the status on page load
            handleOptionalOrRequiredOption();

            // Add an event listener to the status field to handle change events
            if (statusField) {
                statusField.addEventListener("change", handleOptionalOrRequiredOption);
            }
        });


        $("#show_error_message").click(function () {
            let message = " There are group(s) ({{$reminder_promo_tech_teams_text}}) still not transfer CR to Smoke test yet!"
            Swal.fire('Warning...', message, 'error')
        });
        document.addEventListener("DOMContentLoaded", function () {
            const selectStatus = document.querySelector('select[name="new_status_id"]');
            const technicalTeams = document.querySelector('select[name="technical_teams[]"]');
            const techLabel = document.querySelector('.field_technical_teams label');

            if (selectStatus && technicalTeams && techLabel) {
                selectStatus.addEventListener("change", function () {
                    const selectedText = selectStatus.options[selectStatus.selectedIndex].text;


                    if (selectedText === "Pending CD FB" || selectedText === "Request MD's") {
                        // Make technical teams required
                        technicalTeams.setAttribute("required", "required");

                        // Add red asterisk if not already there
                        if (!techLabel.querySelector(".required-mark")) {
                            const span = document.createElement("span");
                            span.textContent = " *";
                            span.style.color = "red";
                            span.classList.add("required-mark");
                            techLabel.appendChild(span);
                        }
                    } else {
                        // Remove required if status is changed away
                        technicalTeams.removeAttribute("required");

                        // Remove the asterisk if it exists
                        const mark = techLabel.querySelector(".required-mark");
                        if (mark) {
                            mark.remove();
                        }
                    }
                });
            }
        });


        // Initialize Select2 for all kt-select2 elements
        jQuery(document).ready(function () {
            $('.kt-select2').select2({
                placeholder: "Select options",
                allowClear: true,
                width: '100%'
            });

            // Reinitialize Select2 after AJAX loads
            $(document).ajaxComplete(function () {
                $('.kt-select2').select2({
                    placeholder: "Select options",
                    allowClear: true,
                    width: '100%'
                });
            });
        });


        // Testable flag and testing estimation handler - Hidden field version
        document.addEventListener('DOMContentLoaded', function () {

            // Get the elements (no checkbox needed)
            const testingEstimationInput = document.querySelector('input[name="testing_estimation"]');
            const testableFlagInput = document.querySelector('input[name="testable_flag"]');
            const statusSelectInput = document.querySelector('select[name="new_status_id"]');
            const statusText = statusSelectInput.options[statusSelectInput.selectedIndex].text.trim(); // visible text

            // Check if elements exist
            if (!testingEstimationInput || !testableFlagInput) {
                console.warn('Testing estimation input or testable_flag hidden field not found');
                return;
            }

            // Function to update UI based on hidden field value
            function updateEstimationFieldState() {
                let flagValue = testableFlagInput.value;

                // Handle empty or undefined values - treat them as '0'
                if (!flagValue || flagValue === '' || flagValue.trim() === '') {
                    flagValue = '0';
                    testableFlagInput.value = '0';
                }

                const isTestable = flagValue === '1';

                if (isTestable && statusText === 'Testing Estimation') {
                    // Enable the input field
                    testingEstimationInput.disabled = false;
                    testingEstimationInput.classList.remove('disabled', 'bg-gray-100');
                    testingEstimationInput.classList.add('bg-white');
                    testingEstimationInput.placeholder = 'Enter testing estimation (must be > 0)';

                    // Add visual feedback to label
                    const label = document.querySelector('label[for="testing_estimation"]');
                    if (label) {
                        label.classList.remove('text-gray-400');
                        label.classList.add('text-gray-700');
                    }

                } else {
                    // Disable the input field and set to 0
                    testingEstimationInput.disabled = true;
                    testingEstimationInput.classList.add('disabled', 'bg-gray-100');
                    testingEstimationInput.classList.remove('bg-white');
                    testingEstimationInput.value = '0';
                    testingEstimationInput.placeholder = 'Testing not required';

                    // Clear any validation errors
                    clearValidationError(testingEstimationInput);

                    // Add visual feedback to label
                    const label = document.querySelector('label[for="testing_estimation"]');
                    if (label) {
                        label.classList.add('text-gray-400');
                        label.classList.remove('text-gray-700');
                    }
                }
            }

            // Function to show validation error
            function showValidationError(input, message) {
                // Remove existing error
                clearValidationError(input);

                // Add error class to input
                input.classList.add('border-red-500', 'focus:border-red-500');
                input.classList.remove('border-gray-300');

                // Create error message element
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-500 text-sm mt-1 validation-error';
                errorDiv.textContent = message;

                // Insert error message after the input
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }

            // Function to clear validation error
            function clearValidationError(input) {
                // Remove error classes
                input.classList.remove('border-red-500', 'focus:border-red-500');
                input.classList.add('border-gray-300');

                // Remove error message
                const errorElement = input.parentNode.querySelector('.validation-error');
                if (errorElement) {
                    errorElement.remove();
                }
            }

            // Function to validate on form submit
            function validateForSubmit() {
                const testableFlagValue = testableFlagInput.value;
                const value = parseFloat(testingEstimationInput.value);
                // const status = statusInput.value;
                // Clear previous validation
                clearValidationError(testingEstimationInput);

                const isTestable = testableFlagValue === '1';

                // console.log(statusText);
                /* if(isTestable && statusSelectInput.value == 41){
                     showValidationError(testingEstimationInput, 'Testing estimation must be greater than 0 when testable is enabled');
                     return false;
                 }*/
                //console.log((!testingEstimationInput.value || isNaN(value)) && statusSelectInput.value == 41);
                if (isTestable && (!testingEstimationInput.value || isNaN(value) || value <= 0) && statusSelectInput.value == 41) {
                    showValidationError(testingEstimationInput, 'Testing estimation must be greater than 0 when testable is enabled 2   ');
                    return false;
                }

                return true;
            }

            // Listen for changes to the hidden field (if changed programmatically)
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        updateEstimationFieldState();
                    }
                });
            });

            // Observe the hidden field for value changes
            observer.observe(testableFlagInput, {
                attributes: true,
                attributeFilter: ['value']
            });

            // Also listen for input events on hidden field
            testableFlagInput.addEventListener('input', updateEstimationFieldState);
            testableFlagInput.addEventListener('change', updateEstimationFieldState);

            // Form submission validation
            const form = testingEstimationInput.closest('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!validateForSubmit()) {
                        e.preventDefault();
                        e.stopPropagation();
                        testingEstimationInput.focus();
                        return false;
                    }
                });
            }

            // Initialize the state based on hidden field value
            updateEstimationFieldState();

            // Function to manually update testable flag (for external use)
            window.updateTestableFlag = function (value) {
                testableFlagInput.value = value ? '1' : '0';
                updateEstimationFieldState();
            };
        });

        // jQuery version - simplified for hidden field only
        if (typeof jQuery !== 'undefined') {
            $(document).ready(function () {

                const $testingEstimationInput = $('input[name="testing_estimation"]');
                const $testableFlagInput = $('input[name="testable_flag"]');

                if ($testingEstimationInput.length === 0 || $testableFlagInput.length === 0) {
                    return;
                }

                function updateEstimationFieldStateJQuery() {
                    let flagValue = $testableFlagInput.val();

                    // Handle empty values
                    if (!flagValue || flagValue === '' || flagValue.trim() === '') {
                        flagValue = '0';
                        $testableFlagInput.val('0');
                    }

                    const isTestable = flagValue === '1';

                    if (isTestable && statusText === 'Testing Estimation') {
                        $testingEstimationInput.prop('disabled', false)
                            .removeClass('disabled bg-gray-100')
                            .addClass('bg-white')
                            .attr('placeholder', 'Enter testing estimation (must be > 0)');

                        $('label[for="testing_estimation"]').removeClass('text-gray-400').addClass('text-gray-700');

                    } else {
                        $testingEstimationInput.prop('disabled', true)
                            .addClass('disabled bg-gray-100')
                            .removeClass('bg-white')
                            .val('0')
                            .attr('placeholder', 'Testing not required');

                        $('label[for="testing_estimation"]').addClass('text-gray-400').removeClass('text-gray-700');
                    }
                }

                // Listen for changes to hidden field
                $testableFlagInput.on('change input', updateEstimationFieldStateJQuery);

                // Initialize
                updateEstimationFieldStateJQuery();

                // Global function for external use
                window.updateTestableFlag = function (value) {
                    $testableFlagInput.val(value ? '1' : '0');
                    updateEstimationFieldStateJQuery();
                };
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form");
            const statusSelect = document.querySelector('select[name="new_status_id"]');
            const originalValue = statusSelect ? statusSelect.value : null;
            if (form) {
                form.addEventListener("submit", function (event) {
                    event.preventDefault();
                    const selectedStatus = statusSelect.value;
                    //     alert( parseInt(window.relevantNotPending));
                    //     alert(selectedStatus);
                    // alert(window.pendingProductionId);
                    // alert(originalValue);
                    // const selectedStatus = document.querySelector('select[name="new_status_id"]').value;

                    if (
                        selectedStatus != window.pendingProductionId && originalValue == window.pendingProductionId &&
                        parseInt(window.relevantNotPending) > 0
                    ) {
                        Swal.fire({
                            title: 'Relevant CRs Not Ready',
                            text: "Some relevant CRs are NOT in Pending Production Deployment. Continue anyway?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, continue',
                            cancelButtonText: 'Cancel'
                        }).then(result => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });

                        return;
                    }
                    form.submit();
                });



            }
        });
    </script>
    <script>
        $(document).ready(function () {
            //console.log('=== CAP USERS DEBUGGING START ===');

            // Multiple approaches to find and hide the cap_users field
            //console.log('Looking for cap_users field...');

            // Approach 1: By class name
            var capUsersField1 = $(".field_cap_users");
            // console.log('Approach 1 - found by class:', capUsersField1.length, capUsersField1);

            // Approach 2: By input name
            var capUsersField2 = $("select[name='cap_users[]']");
            //console.log('Approach 2 - found by name:', capUsersField2.length, capUsersField2);

            // Approach 3: By label text
            var capUsersField3 = $("label:contains('Cab Users')").closest('div');
            //console.log('Approach 3 - found by label:', capUsersField3.length, capUsersField3);

            // Force hide initially with all approaches
            capUsersField1.hide();
            capUsersField2.hide();
            capUsersField3.hide();

            // Remove required attribute
            $('select[name="cap_users[]"]').prop('required', false);

            // Check status every 100ms for the first 10 seconds
            let checkCount = 0;
            const checkInterval = setInterval(function () {
                checkCount++;
                const statusSelect = $('select[name="new_status_id"]');
                const statusText = statusSelect.find('option:selected').text();
                //console.log('=== STATUS CHECK #' + checkCount + ' ===');
                //console.log('Status text:', '"' + statusText + '"');
                //console.log('Status select exists:', statusSelect.length > 0);

                // Show cap_users for Pending CAB, Pending CAB Approval
                if (statusText.includes('Pending CAB') || statusText.includes('Pending CAB Approval')) {
                    //console.log(' SHOWING cap_users - status matches CAB conditions');

                    // Try multiple show methods
                    capUsersField1.show();
                    capUsersField2.show();
                    capUsersField3.show();

                    // Force show with CSS
                    $('.field_cap_users').css('display', 'block');
                    $('.field_cap_users').css('visibility', 'visible');
                    $('.field_cap_users').removeClass('hidden');

                    // Set required
                    $('select[name="cap_users[]"]').prop('required', true);
                    $('select[name="cap_users[]"]').attr('required', 'required');
                }
                // Hide cap_users for Pending CR Document Validation
                else if (statusText.includes('Pending CR Document Validation') ||
                    statusText.includes('Pending Update CR Doc')) {
                    //console.log(' HIDING cap_users - status matches document validation conditions');

                    // Try multiple hide methods
                    capUsersField1.hide();
                    capUsersField2.hide();
                    capUsersField3.hide();

                    // Force hide with CSS
                    $('.field_cap_users').css('display', 'none !important');
                    $('.field_cap_users').css('visibility', 'hidden');
                    $('.field_cap_users').addClass('hidden');

                    // Remove required
                    $('select[name="cap_users[]"]').prop('required', false);
                    $('select[name="cap_users[]"]').removeAttr('required');
                }
                // Hide for all other statuses
                else {
                    //console.log(' HIDING cap_users - other status');
                    capUsersField1.hide();
                    capUsersField2.hide();
                    capUsersField3.hide();
                    $('.field_cap_users').css('display', 'none !important');
                    $('select[name="cap_users[]"]').prop('required', false);
                }

                // Stop checking after 10 seconds
                if (checkCount >= 100) {
                    clearInterval(checkInterval);
                    //console.log('=== STOPPED CHECKING AFTER 10 SECONDS ===');
                }
            }, 100);

            // Also check on status change
            $(document).on('change', 'select[name="new_status_id"]', function () {
                const statusText = $(this).find('option:selected').text();
                //console.log('=== STATUS CHANGED ===');
                //console.log('New status:', '"' + statusText + '"');

                if (statusText.includes('Pending CAB') || statusText.includes('Pending CAB Approval')) {
                    //console.log(' STATUS CHANGE: SHOWING cap_users');
                    $(".field_cap_users").show();
                    $('.field_cap_users').css('display', 'block');
                    $('select[name="cap_users[]"]').prop('required', true);
                }
                else {
                    //console.log(' STATUS CHANGE: HIDING cap_users');
                    $(".field_cap_users").hide();
                    $('.field_cap_users').css('display', 'none !important');
                    $('select[name="cap_users[]"]').prop('required', false);
                }
            });
        });
    </script>





    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const statusSelect = document.querySelector('select[name="new_status_id"]');
            const techTeamWrapper = document.querySelector('.change-request-form-field select[name="technical_teams[]"]')?.closest('.change-request-form-field');
            const techTeamSelect = document.querySelector('select[name="technical_teams[]"]');

            function addAsteriskIfNeeded(wrapper) {
                const label = wrapper.querySelector("label");
                if (label && !label.innerHTML.includes('*')) {
                    const star = document.createElement("span");
                    star.style.color = "red";
                    star.innerHTML = " *";
                    label.appendChild(star);
                }
            }

            function removeAsterisk(wrapper) {
                const label = wrapper.querySelector("label");
                if (label) {
                    label.innerHTML = label.innerHTML.replace(/\s*<span[^>]*>\*<\/span>/g, '').replace(/\s*\*/g, '');
                }
            }

            function handleStatusChange(value) {
                const hideStatuses = ["260", "223", "273"];
                const requiredStatuses = ["257", "220", "276", "275"];
                const hideTexts = ["Test in Progress", "Pending HL Design", "Assess the defects"];

                if (!techTeamWrapper || !techTeamSelect) return;

                const selectedOption = statusSelect?.options[statusSelect.selectedIndex];
                const selectedText = selectedOption?.textContent.trim();

                if (hideStatuses.includes(value) || hideTexts.includes(selectedText)) {
                    techTeamWrapper.style.display = "none";
                    techTeamSelect.removeAttribute("required");
                    removeAsterisk(techTeamWrapper);
                } else {
                    techTeamWrapper.style.display = "";
                    if (requiredStatuses.includes(value)) {
                        techTeamSelect.setAttribute("required", "required");
                        addAsteriskIfNeeded(techTeamWrapper);
                    } else {
                        techTeamSelect.removeAttribute("required");
                        removeAsterisk(techTeamWrapper);
                    }
                }
            }

            if (statusSelect) {
                handleStatusChange(statusSelect.value);
                statusSelect.addEventListener("change", function () {
                    handleStatusChange(this.value);
                });
            }
        });
    </script>

    {{-- Rejection Reason Script --}}
    @if(isset($cr))
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const select = document.querySelector('select[name="new_status_id"]');
                const reasonWrapper = document.getElementById('reason-wrapper');

                if (!select || !reasonWrapper) return;

                select.addEventListener('change', function () {
                    const selectedLabel = this.options[this.selectedIndex].text.trim();

                    if (selectedLabel.toLowerCase() === "reject") {
                        if (!document.querySelector('select[name="reason"]')) {
                            const reasonSelect = document.createElement('select');
                            reasonSelect.name = "reason";
                            reasonSelect.classList.add("form-control", "mt-2");
                            reasonWrapper.appendChild(reasonSelect);
                        }
                    } else {
                        const existing = document.querySelector('select[name="reason"]');
                        if (existing) {
                            existing.remove();
                        }
                    }
                });
            });
        </script>
    @endif

    {{-- Defects Confirmation Script --}}

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Prevent multiple initializations
            if (window.checkStatusBeforeSubmitInitialized) return;
            window.checkStatusBeforeSubmitInitialized = true;

            const statusSelect = document.querySelector('select[name="new_status_id"]');

            // Find form by traversing up from the select
            const form = statusSelect ? statusSelect.closest("form") : null;

            if (form) {
                console.log("Change Request Form found. Attaching CAPTURE-PHASE listener.");

                // Use capture phase (third argument = true) to intercept event BEFORE bubbling handlers
                form.addEventListener("submit", function (event) {
                    console.log("Submit captured!");

                    const selectElement = form.querySelector('select[name="new_status_id"]');
                    const selectedOption = selectElement?.options[selectElement.selectedIndex];
                    const selectedText = selectedOption?.textContent.trim();
                    const defectValue = selectedOption?.getAttribute('data-defect') || "0";

                    console.log("Checking:", { defectValue, selectedText });

                    if (defectValue === "1" && selectedText === "Final UAT Results & FB") {
                        console.log("Blocking submit for alert (Capture Phase)");
                        event.preventDefault();
                        event.stopPropagation();
                        event.stopImmediatePropagation();

                        // Show Alert
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "There are defects related to this CRS. Are you sure you want to continue?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, continue!',
                            cancelButtonText: 'No, cancel!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                console.log("Alert Confirmed -> Submitting Programmatically");
                                // Submit programmatically (bypasses listeners)
                                form.submit();
                            }
                        });

                        return false;
                    }
                    console.log("Allowing submit");
                }, true); // <--- TRUE for Capture Phase
            } else {
                console.warn("Change Request Form NOT found for defect check script!");
            }
        });
    </script>




@endpush