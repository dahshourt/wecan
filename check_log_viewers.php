<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING LOG_VIEWERS TABLE ===" . PHP_EOL;

// Check if table exists
$tableExists = DB::getSchemaBuilder()->hasTable('log_viewers');
echo "Table exists: " . ($tableExists ? "YES" : "NO") . PHP_EOL;

if (!$tableExists) {
    echo "❌ log_viewers table does not exist!" . PHP_EOL;
    exit(1);
}

// Get total count
$totalCount = DB::table('log_viewers')->count();
echo "Total records: " . $totalCount . PHP_EOL;

if ($totalCount == 0) {
    echo "No logs found in log_viewers table." . PHP_EOL;
    exit(0);
}

echo PHP_EOL . "=== RECENT LOGS (Last 20) ===" . PHP_EOL;

$recentLogs = DB::table('log_viewers')
    ->orderBy('id', 'desc')
    ->limit(20)
    ->get();

foreach ($recentLogs as $log) {
    echo "ID: " . $log->id . 
         " | Level: " . $log->level_name .
         " | Time: " . $log->created_at .
         PHP_EOL;
    echo "Message: " . substr($log->message, 0, 100) . (strlen($log->message) > 100 ? "..." : "") . PHP_EOL;
    
    if ($log->context) {
        $context = json_decode($log->context, true);
        if ($context && isset($context['cr_id']) && $context['cr_id'] == 31351) {
            echo "🔍 CR 31351 RELATED: " . json_encode($context, JSON_PRETTY_PRINT) . PHP_EOL;
        }
    }
    echo str_repeat("-", 80) . PHP_EOL;
}

echo PHP_EOL . "=== CR 31351 RELATED LOGS ===" . PHP_EOL;

$crLogs = DB::table('log_viewers')
    ->whereRaw("JSON_EXTRACT(context, '$.cr_id') = '31351'")
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

echo "CR 31351 related logs: " . $crLogs->count() . PHP_EOL;

foreach ($crLogs as $log) {
    echo "ID: " . $log->id . 
         " | Level: " . $log->level_name .
         " | Time: " . $log->created_at .
         PHP_EOL;
    echo "Message: " . $log->message . PHP_EOL;
    
    if ($log->context) {
        echo "Context: " . $log->context . PHP_EOL;
    }
    echo str_repeat("-", 80) . PHP_EOL;
}

echo PHP_EOL . "=== ERROR LOGS (Last 10) ===" . PHP_EOL;

$errorLogs = DB::table('log_viewers')
    ->where('level_name', 'ERROR')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

echo "Error logs: " . $errorLogs->count() . PHP_EOL;

foreach ($errorLogs as $log) {
    echo "ID: " . $log->id . 
         " | Time: " . $log->created_at .
         PHP_EOL;
    echo "Message: " . $log->message . PHP_EOL;
    
    if ($log->trace_stack) {
        echo "Trace: " . substr($log->trace_stack, 0, 200) . "..." . PHP_EOL;
    }
    echo str_repeat("-", 80) . PHP_EOL;
}
