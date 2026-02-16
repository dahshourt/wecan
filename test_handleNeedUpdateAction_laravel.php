<?php

/**
 * Test script for handleNeedUpdateAction function
 * Run this within Laravel context: php artisan tinker
 * Then: include 'test_handleNeedUpdateAction_laravel.php';
 */

use App\Services\ChangeRequest\ChangeRequestStatusService;
use App\Models\ChangeRequest;
use App\Models\ChangeRequestStatus;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleNeedUpdateActionTester
{
    private $service;
    private $testResults = [];

    public function __construct()
    {
        $this->service = new ChangeRequestStatusService();
    }

    /**
     * Run all tests
     */
    public function runAllTests()
    {
        echo "=== Testing handleNeedUpdateAction Function ===\n\n";

        $this->testFunctionExists();
        $this->testWithInvalidCrId();
        $this->testWithValidCrId();
        $this->testStatusNameMappings();
        $this->testDatabaseOperations();

        $this->printSummary();
    }

    /**
     * Test 1: Check if function exists
     */
    private function testFunctionExists()
    {
        echo "Test 1: Function Existence\n";
        
        if (method_exists($this->service, 'handleNeedUpdateAction')) {
            $this->testResults['function_exists'] = true;
            echo "✓ Function handleNeedUpdateAction exists\n";
        } else {
            $this->testResults['function_exists'] = false;
            echo "✗ Function handleNeedUpdateAction does not exist\n";
        }
        echo "\n";
    }

    /**
     * Test 2: Test with invalid CR ID
     */
    private function testWithInvalidCrId()
    {
        echo "Test 2: Invalid CR ID Handling\n";
        
        try {
            $result = $this->service->handleNeedUpdateAction(999999);
            $this->testResults['invalid_cr_id'] = false;
            echo "✗ Should have thrown exception for invalid CR ID\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Change Request not found') !== false) {
                $this->testResults['invalid_cr_id'] = true;
                echo "✓ Correctly throws exception for invalid CR ID: " . $e->getMessage() . "\n";
            } else {
                $this->testResults['invalid_cr_id'] = false;
                echo "✗ Wrong exception message: " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }

    /**
     * Test 3: Test with valid CR ID
     */
    private function testWithValidCrId()
    {
        echo "Test 3: Valid CR ID Processing\n";
        
        // Get a real CR from database
        $cr = ChangeRequest::first();
        if (!$cr) {
            echo "⚠ No Change Requests found in database, skipping test\n\n";
            return;
        }

        echo "Testing with CR ID: {$cr->id}\n";
        
        try {
            // Check current status before
            $beforeStatuses = ChangeRequestStatus::where('cr_id', $cr->id)
                ->where('active', '1')
                ->count();
            
            echo "Before: {$beforeStatuses} active statuses\n";
            
            $result = $this->service->handleNeedUpdateAction($cr->id);
            
            // Check status after
            $afterStatuses = ChangeRequestStatus::where('cr_id', $cr->id)
                ->where('active', '1')
                ->count();
            
            echo "After: {$afterStatuses} active statuses\n";
            echo "Function returned: " . ($result ? 'true' : 'false') . "\n";
            
            $this->testResults['valid_cr_id'] = is_bool($result);
            echo "✓ Function executed without errors\n";
            
        } catch (Exception $e) {
            $this->testResults['valid_cr_id'] = false;
            echo "✗ Error processing valid CR ID: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    /**
     * Test 4: Test status name mappings
     */
    private function testStatusNameMappings()
    {
        echo "Test 4: Status Name Mappings\n";
        
        $parallelStatusNames = [
            'Pending Agreed Scope Approval-SA',
            'Pending Agreed Scope Approval-Vendor',
            'Pending Agreed Scope Approval-Business',
            'Request Draft CR Doc'
        ];
        
        $allFound = true;
        $foundStatuses = [];
        
        foreach ($parallelStatusNames as $statusName) {
            $status = Status::where('status_name', $statusName)
                ->where('active', '1')
                ->first();
            
            if ($status) {
                $foundStatuses[] = $statusName;
                echo "✓ Found status: {$statusName} (ID: {$status->id})\n";
            } else {
                $allFound = false;
                echo "✗ Missing status: {$statusName}\n";
            }
        }
        
        $this->testResults['status_mappings'] = $allFound;
        echo "Statuses found: " . count($foundStatuses) . "/" . count($parallelStatusNames) . "\n\n";
    }

    /**
     * Test 5: Test database operations
     */
    private function testDatabaseOperations()
    {
        echo "Test 5: Database Operations\n";
        
        try {
            // Test if we can query the tables
            $crCount = ChangeRequest::count();
            $statusCount = ChangeRequestStatus::count();
            
            echo "✓ Change Requests table accessible ({$crCount} records)\n";
            echo "✓ Change Request Statuses table accessible ({$statusCount} records)\n";
            
            // Test if active scope works
            $activeStatuses = ChangeRequestStatus::active()->count();
            echo "✓ Active scope works ({$activeStatuses} active records)\n";
            
            $this->testResults['database_operations'] = true;
            
        } catch (Exception $e) {
            $this->testResults['database_operations'] = false;
            echo "✗ Database operation failed: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    /**
     * Print test summary
     */
    private function printSummary()
    {
        echo "=== Test Summary ===\n";
        
        $totalTests = count($this->testResults);
        $passedTests = array_sum($this->testResults);
        
        foreach ($this->testResults as $test => $result) {
            $status = $result ? '✓ PASS' : '✗ FAIL';
            echo "{$status} {$test}\n";
        }
        
        echo "\nResults: {$passedTests}/{$totalTests} tests passed\n";
        
        if ($passedTests === $totalTests) {
            echo "🎉 All tests passed! Function appears to be working correctly.\n";
        } else {
            echo "⚠ Some tests failed. Review the issues above.\n";
        }
    }

    /**
     * Test specific scenario: CR with parallel statuses
     */
    public function testWithParallelStatuses()
    {
        echo "Test: CR with Parallel Statuses\n";
        
        // Find a CR that has parallel statuses
        $parallelStatusNames = [
            'Pending Agreed Scope Approval-SA',
            'Pending Agreed Scope Approval-Vendor',
            'Pending Agreed Scope Approval-Business',
            'Request Draft CR Doc'
        ];
        
        $statusIds = [];
        foreach ($parallelStatusNames as $statusName) {
            $status = Status::where('status_name', $statusName)
                ->where('active', '1')
                ->first();
            if ($status) {
                $statusIds[] = $status->id;
            }
        }
        
        if (empty($statusIds)) {
            echo "⚠ No parallel statuses found in database, skipping test\n";
            return;
        }
        
        $crWithParallelStatuses = ChangeRequestStatus::whereIn('new_status_id', $statusIds)
            ->where('active', '1')
            ->first();
        
        if (!$crWithParallelStatuses) {
            echo "⚠ No CR with parallel statuses found, skipping test\n";
            return;
        }
        
        echo "Testing CR with parallel statuses: {$crWithParallelStatuses->cr_id}\n";
        
        try {
            $beforeCount = ChangeRequestStatus::where('cr_id', $crWithParallelStatuses->cr_id)
                ->whereIn('new_status_id', $statusIds)
                ->where('active', '1')
                ->count();
            
            echo "Before: {$beforeCount} parallel active statuses\n";
            
            $result = $this->service->handleNeedUpdateAction($crWithParallelStatuses->cr_id);
            
            $afterCount = ChangeRequestStatus::where('cr_id', $crWithParallelStatuses->cr_id)
                ->whereIn('new_status_id', $statusIds)
                ->where('active', '1')
                ->count();
            
            echo "After: {$afterCount} parallel active statuses\n";
            echo "Function returned: " . ($result ? 'true' : 'false') . "\n";
            
            if ($beforeCount > 0 && $afterCount === 0) {
                echo "✓ Parallel statuses were successfully deactivated\n";
            } else {
                echo "⚠ Parallel status behavior unexpected\n";
            }
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
}

// Run the tests
$tester = new HandleNeedUpdateActionTester();
$tester->runAllTests();

// Additional test for parallel statuses
$tester->testWithParallelStatuses();

echo "\n=== Test Complete ===\n";
