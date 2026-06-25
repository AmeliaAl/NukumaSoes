<?php
$inventories = \App\Models\Inventory::whereNotNull('tgl_expired')
    ->whereDate('tgl_expired', '>', now())
    ->orderBy('tgl_expired', 'asc')
    ->get();
echo "COUNT: " . $inventories->count() . "\n";
foreach($inventories as $inv) {
    echo $inv->kode_produk . " - " . $inv->no_batch . " - " . $inv->status . " - " . $inv->tgl_expired . "\n";
}
