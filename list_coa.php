<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$coas = \App\Models\Coa::all();
foreach ($coas as $c) {
    echo "ID: {$c->id} | Kode: {$c->kode_akun} | Nama: {$c->nama_akun}\n";
}
