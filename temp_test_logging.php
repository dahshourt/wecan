<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Log;

echo "=== TESTING DATABASE LOGGING ===" . PHP_EOL;

// Test different log levels
Log::info('Test info log for CR 31351', [
    'cr_id' => 31351,
    'test' => true,
    'timestamp' => now()->toDateTimeString()
]);

Log::debug('Test debug log for CR 31351', [
    'cr_id' => 31351,
    'test' => true,
    'level' => 'debug'
]);

Log::warning('Test warning log for CR 31351', [
    'cr_id' => 31351,
    'test' => true,
    'level' => 'warning'
]);

echo "Test logs written. Checking database..." . PHP_EOL;

// Check if logs were written to database
use Illuminate\Support\Facades\DB;
$count = DB::table('log_viewers')->count();
echo "Total logs in database: " . $count . PHP_EOL;

if ($count > 0) {
    echo "✅ Database logging is working!" . PHP_EOL;
    
    // Show recent logs
    $recent = DB::table('log_viewers')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
        
    echo PHP_EOL . "Recent logs:" . PHP_EOL;
    foreach ($recent as $log) {
        echo "ID: " . $log->id . " | " . $log->level_name . " | " . $log->message . PHP_EOL;
    }
} else {
    echo "❌ Database logging is not working. Logs are still going to files." . PHP_EOL;
}
