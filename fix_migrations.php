<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $migrations = [
        '0001_01_01_000000_create_users_table',
        '0001_01_01_000001_create_cache_table',
        '0001_01_01_000002_create_jobs_table'
    ];

    foreach ($migrations as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if (!$exists) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 3
            ]);
            echo "Added $migration to migrations table.\n";
        } else {
            echo "$migration already in migrations table.\n";
        }
    }
    
    // Also remove the old ones if they are conflicting, but they usually don't conflict unless filename matches.
    
    echo "Done resolving migration mismatch.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
