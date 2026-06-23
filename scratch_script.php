<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = [
    'App\Models\Barang',
    'App\Models\PenjualanNonKonsinyasi',
    'App\Models\PenjualanKonsinyasi',
    'App\Models\LaporanKonsinyasi',
    'App\Models\TagihanKonsinyasi',
    'App\Models\Pembayaran',
    'App\Models\Mitra',
    'App\Models\Pelanggan',
    'App\Models\SalesOrder',
    'App\Models\SaldoAwal',
    'App\Models\JurnalUmum',
    'App\Models\ReturPenjualan',
];

$output = [];
foreach ($models as $class) {
    if (!class_exists($class)) continue;
    
    $model = new $class;
    $table = $model->getTable();
    $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
    
    $reflection = new ReflectionClass($class);
    $methods = [];
    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if ($method->class == $class) {
            $methods[] = $method->getName();
        }
    }
    
    $output[] = [
        'class' => class_basename($class),
        'attributes' => $columns,
        'methods' => $methods
    ];
}

echo json_encode($output, JSON_PRETTY_PRINT);
