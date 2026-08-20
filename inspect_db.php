<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$conn = $app->make('db')->connection();

echo 'has_table=' . ($conn->getSchemaBuilder()->hasTable('detail_persediaan_produk') ? 'yes' : 'no') . PHP_EOL;
echo 'has_barang=' . ($conn->getSchemaBuilder()->hasTable('barang') ? 'yes' : 'no') . PHP_EOL;
echo 'migration_row=' . $conn->table('migrations')->where('migration', '2026_05_19_072750_create_detail_persediaan_produk_table')->count() . PHP_EOL;
echo 'row_count=' . $conn->table('detail_persediaan_produk')->count() . PHP_EOL;
$create = $conn->selectOne('SHOW CREATE TABLE detail_persediaan_produk');
if ($create) {
    echo 'create_sql=' . preg_replace('/\s+/', ' ', $create->{'Create Table'}) . PHP_EOL;
}

