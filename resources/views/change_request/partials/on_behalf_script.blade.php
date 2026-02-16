@if(auth()->check() && auth()->user()->default_group == 8)
<script>
    // Handle 'On Behalf' checkbox behaviour for requester_department
    (function () {
        // Shared logic to lock/unlock
        function toggleDepartmentLock(isLocked) {
             const $dept = $('select[name="requester_department"], input[name="requester_department"]');
             const techText = 'Information Technology';

             if (isLocked) {
                 // LOCK to Information Technology
                 console.log('Debug: Locking department to', techText);
                 
                 // Set value
                 if ($dept.is('select')) {
                     const $techOption = $dept.find('option').filter(function () { return $(this).text().trim() === techText; });
                     if ($techOption.length) $dept.val($techOption.val());
                 } else {
                     $dept.val(techText);
                 }

                 // Apply Visuals & Readonly
                 $dept.addClass('readonly-select');
                 $dept.prop('readonly', true);
                 $dept.attr('aria-readonly', 'true');
                 $dept.css({
                    'background-color': '#e9ecef',
                    'color': '#6c757d',
                    'pointer-events': 'none',
                    'touch-action': 'none'
                });
                
                // Handle Select2
                if ($dept.data('select2') || $dept.next('.select2-container').length) {
                    $dept.trigger('change.select2'); // Update UI value
                    const $container = $dept.next('.select2-container');
                    $container.find('.select2-selection').css({ 'background-color': '#e9ecef', 'pointer-events': 'none' });
                    
                     // Block events
                    $container.off('.readonlyLock').on('mousedown.readonlyLock', function(e){ e.preventDefault(); e.stopPropagation(); return false; });
                }

                // Block native events
                $dept.off('.readonlyLock');
                $dept.on('select2:opening.readonlyLock', function (e) { e.preventDefault(); return false; });
                $dept.on('mousedown.readonlyLock keydown.readonlyLock', function (e) { e.preventDefault(); this.blur(); return false; });

             } else {
                 // UNLOCK
                 console.log('Debug: Unlocking department');
                 
                 // Remove Visuals & Readonly
                 $dept.removeClass('readonly-select');
                 $dept.prop('readonly', false);
                 $dept.removeAttr('aria-readonly');
                 $dept.css({
                    'background-color': '',
                    'color': '',
                    'pointer-events': '',
                    'touch-action': ''
                });

                // Handle Select2
                if ($dept.data('select2') || $dept.next('.select2-container').length) {
                    const $container = $dept.next('.select2-container');
                    $container.find('.select2-selection').css({ 'background-color': '', 'pointer-events': '' });
                    $container.off('.readonlyLock');
                }

                // Remove native blocks
                $dept.off('.readonlyLock');
             }
        }

        // Delegated listener for checkbox
        $(document).on('change', 'input[name="on_behalf"]:checkbox', function () {
            const checked = $(this).prop('checked');
            console.log('Debug: On Behalf changed to:', checked);
            
            // Logic Reversed: 
            // Checked = Unlocked (User can select anything)
            // Unchecked = Locked (Forced to IT)
            toggleDepartmentLock(!checked); 
            
            // maintain visual checked class
            $(this).parent().toggleClass('checked', checked);
        });

        // Initialize on page load
        $(function () {
             const $checkbox = $('input[name="on_behalf"]:checkbox');
             if ($checkbox.length) {
                 const checked = $checkbox.prop('checked');
                 // Initial state match
                 toggleDepartmentLock(!checked);
             }
        });
    })();

    // Aggressive Revert Logic (Reversed)
    // If On Behalf is UNCHECKED, we must FORCE "Information Technology"
    $(document).on('change input', 'select[name="requester_department"], input[name="requester_department"]', function (e) {
        const $dept = $(this);
        // CRITICAL FIX: Ensure we select the CHECKBOX, not the hidden input
        const $onBehalf = $('input[name="on_behalf"]:checkbox');
        
        // Only intervene if On Behalf is UNCHECKED (User should not be able to change it)
        if ($onBehalf.length && !$onBehalf.is(':checked')) {
             const techText = 'Information Technology';
             let isCorrect = false;

             if ($dept.is('select')) {
                 const $selected = $dept.find('option:selected');
                 if ($selected.text().trim() === techText) isCorrect = true;
             } else {
                 if ($dept.val() === techText) isCorrect = true;
             }

             if (!isCorrect) {
                 console.warn('Security Warning: User attempted to change Department while On Behalf is UNCHECKED. Reverting.');
                 
                 // Force set value
                 if ($dept.is('select')) {
                     const $techOption = $dept.find('option').filter(function () { return $(this).text().trim() === techText; });
                     if ($techOption.length) $dept.val($techOption.val());
                 } else {
                     $dept.val(techText);
                 }
                 
                 // Re-apply lock
                 $dept.prop('readonly', true);
                 // Trigger select2 update if needed
                 if ($dept.data('select2')) $dept.trigger('change.select2');
             }
        }
    });
</script>
@endif
