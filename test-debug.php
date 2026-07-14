<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SaldoAwal;

$saldoAwalPertama = SaldoAwal::orderBy('tanggal')->first();
if ($saldoAwalPertama) {
    echo "Found SaldoAwal ID: " . $saldoAwalPertama->id . "\n";
    echo "tanggal type: " . gettype($saldoAwalPertama->tanggal) . "\n";
    echo "tanggal value: ";
    var_dump($saldoAwalPertama->tanggal);
    
    echo "Carbon parse result type: " . gettype(\Carbon\Carbon::parse($saldoAwalPertama->tanggal)) . "\n";
    try {
        $res = \Carbon\Carbon::parse($saldoAwalPertama->tanggal)->format('Y-m-d');
        echo "format result: " . $res . "\n";
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
} else {
    echo "No SaldoAwal found.\n";
}
