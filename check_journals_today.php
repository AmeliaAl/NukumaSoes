<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$journals = \App\Models\JurnalUmum::whereDate('created_at', '2026-05-06')->get();
foreach ($journals as $j) {
    echo "ID: {$j->id} | Ket: {$j->keterangan} | Debit: {$j->debit}\n";
}
