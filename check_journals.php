<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$journals = \App\Models\JurnalUmum::latest()->take(50)->get();
foreach ($journals as $j) {
    echo "ID: {$j->id} | Tanggal: {$j->tanggal} | Ket: {$j->keterangan} | Debit: {$j->debit} | Kredit: {$j->kredit}\n";
}
