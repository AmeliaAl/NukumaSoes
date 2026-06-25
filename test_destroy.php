<?php 
require 'vendor/autoload.php'; 
$app = require_once 'bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 
try { 
    $entry = App\Models\PersediaanEntry::first(); 
    if ($entry) { 
        $controller = app(App\Http\Controllers\PersediaanController::class); 
        $controller->destroy($entry->id); 
        echo 'Success'; 
    } else { 
        echo 'No entry found'; 
    } 
} catch (\Exception $e) { 
    echo $e->getMessage(); 
}
