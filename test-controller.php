<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Calling SaldoAwalController->create()...\n";
    $controller = new \App\Http\Controllers\SaldoAwalController();
    $res = $controller->create();
    echo "Success calling create()!\n";
} catch (\Throwable $e) {
    echo "Caught Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . " in file: " . $e->getFile() . "\n";
    echo $e->getTraceAsString() . "\n";
}
