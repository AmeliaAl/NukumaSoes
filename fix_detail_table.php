<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$conn = $app->make('db')->connection();

$schema = $conn->getSchemaBuilder();

if (!$schema->hasTable('barang')) {
    echo "ERROR: barang table does not exist\n";
    exit(1);
}

if (!$schema->hasTable('detail_persediaan_produk')) {
    echo "ERROR: detail_persediaan_produk table does not exist\n";
    exit(1);
}

$constraintExists = $conn->selectOne(
    "SELECT COUNT(*) AS cnt FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'detail_persediaan_produk' AND CONSTRAINT_NAME = 'detail_persediaan_produk_barang_id_foreign'"
);

if (!$constraintExists->cnt) {
    echo "Adding missing foreign key constraint...\n";
    $conn->statement(
        "ALTER TABLE detail_persediaan_produk ADD CONSTRAINT detail_persediaan_produk_barang_id_foreign FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE"
    );
    echo "Foreign key added.\n";
} else {
    echo "Foreign key constraint already exists.\n";
}

$migration = '2026_05_19_072750_create_detail_persediaan_produk_table';
$batch = $conn->table('migrations')->max('batch') ?: 1;
if (!$conn->table('migrations')->where('migration', $migration)->exists()) {
    $conn->table('migrations')->insert(['migration' => $migration, 'batch' => $batch]);
    echo "Inserted migration record for $migration with batch $batch.\n";
} else {
    echo "Migration record already exists.\n";
}

echo "Done.\n";
