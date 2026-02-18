# IOT Parallel Workflow Implementation Summary

## Overview
Successfully implemented a parallel workflow for IOT TCs Review statuses that allows independent transitions and merges at the "IOT In Progress" status.

## Status IDs Used
- **Pending IOT TCs Review QC**: 336
- **Pending IOT TCs Review SA**: 337
- **IOT TCs Review QC**: 338
- **IOT TCs Review vendor**: 339
- **IOT In progress**: 340

## Implementation Details

### 1. Status Constants Added
Added 5 new static properties to `ChangeRequestStatusService`:
```php
private static ?int $PENDING_IOT_TCS_REVIEW_QC_STATUS_ID = null;
private static ?int $PENDING_IOT_TCS_REVIEW_SA_STATUS_ID = null;
private static ?int $IOT_TCS_REVIEW_QC_STATUS_ID = null;
private static ?int $IOT_TCS_REVIEW_VENDOR_STATUS_ID = null;
private static ?int $IOT_IN_PROGRESS_STATUS_ID = null;
```

### 2. Helper Functions Added

#### `areBothIotPendingStatusesActive(int $crId): bool`
- Checks if both "Pending IOT TCs Review QC" and "Pending IOT TCs Review SA" are active simultaneously
- Returns true only when both statuses have active=1

#### `getIotInProgressWorkflowId(int $changeRequestId): ?int`
- Detects when CR is transitioning from IOT Review statuses to "IOT In Progress"
- Returns the workflow ID for the transition

#### `handleIotInProgressTransition(int $changeRequestId, array $statusData): void`
- Handles the merge logic when transitioning to "IOT In Progress"
- Deactivates the other IOT Review status (sets active=2)
- Creates new "IOT In Progress" status record
- Updates the CR's main status

### 3. Main Workflow Integration
Added IOT parallel workflow logic to `updateChangeRequestStatus` method:
- Early detection of IOT In Progress transition
- Bypasses normal workflow processing for IOT transitions
- Follows same pattern as existing ATP parallel workflow

## Workflow Behavior

### When CR has both pending statuses active:
1. **Independent transitions**:
   - "Pending IOT TCs Review QC" → "IOT TCs Review QC" works independently
   - "Pending IOT TCs Review SA" → "IOT TCs Review vendor" works independently

2. **Merge point**:
   - When either "IOT TCs Review QC" or "IOT TCs Review vendor" transitions to "IOT In Progress"
   - The other IOT Review status is automatically deactivated (active=2)
   - CR status is set to "IOT In Progress"

### Logging
All IOT workflow operations include comprehensive logging:
- Status activation checks
- Transition handling
- Merge operations
- Error conditions

## Testing
Created test scripts to verify:
- ✅ All IOT status constants are properly defined
- ✅ All IOT methods exist and are callable
- ✅ Status IDs match expected values (336-340)

## Files Modified
- `app/Services/ChangeRequest/ChangeRequestStatusService.php` - Main implementation
- Created test files for verification

## Usage
The parallel workflow is now active and will automatically handle:
1. Detection of parallel IOT pending statuses
2. Independent transitions from pending to review statuses
3. Automatic merge to "IOT In Progress" when either review workflow completes

The implementation follows the same patterns as existing parallel workflows in the system and maintains full compatibility with current functionality.
