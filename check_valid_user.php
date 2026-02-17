<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking for valid user\n";
echo "=====================\n\n";

$user = \App\Models\User::first();
if ($user) {
    echo "Found valid user: ID {$user->id} - {$user->name}\n";
} else {
    echo "No users found in database\n";
}

echo "\nCheck complete.\n";
