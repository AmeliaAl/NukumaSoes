<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$invs = \App\Models\Inventory::where('status', 'Expired')->get();
echo "Found " . $invs->count() . " expired inventories.\n";
foreach($invs as $inv) {
    echo "ID: " . $inv->id . ", Batch: " . $inv->no_batch . ", Kategori: " . $inv->kategori . "\n";
}
