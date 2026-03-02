-- Fix CR 31351 by setting the correct current status
-- The last active status should be "Fix Defect-3rd Parties" (ID: 343)

-- Step 1: Update the main CR record with the correct current status
UPDATE change_request 
SET status_id = 343, 
    updated_at = NOW() 
WHERE id = 31351;

-- Step 2: Verify the latest status record is set to active=1
UPDATE change_request_statuses 
SET active = '1' 
WHERE cr_id = 31351 
  AND new_status_id = 343 
  AND id = (SELECT MAX(id) FROM change_request_statuses WHERE cr_id = 31351 AND new_status_id = 343);

-- Step 3: Check results
SELECT 'CR Status' as info, id, cr_no, status_id, title FROM change_request WHERE id = 31351;
SELECT 'Active Statuses' as info, id, old_status_id, new_status_id, active FROM change_request_statuses WHERE cr_id = 31351 AND active = '1';
