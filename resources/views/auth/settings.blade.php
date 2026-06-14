@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- System Status Card -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-server text-primary me-2"></i> Informasi Server & Aplikasi
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Status runtime dan metadata platform.</p>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tbody>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="ps-0 py-3 text-muted"><i class="fas fa-desktop me-2 text-primary"></i> Nama Aplikasi</td>
                                    <td class="text-end fw-semibold text-dark">{{ $systemInfo['app_name'] }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="ps-0 py-3 text-muted"><i class="fas fa-code-branch me-2 text-primary"></i> Versi Aplikasi</td>
                                    <td class="text-end fw-semibold text-dark">
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2" style="border-radius: 12px;">v{{ $systemInfo['app_version'] }}</span>
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="ps-0 py-3 text-muted"><i class="fab fa-laravel me-2 text-danger"></i> Versi Laravel</td>
                                    <td class="text-end fw-semibold text-dark">v{{ $systemInfo['laravel_version'] }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="ps-0 py-3 text-muted"><i class="fab fa-php me-2 text-primary"></i> Versi PHP</td>
                                    <td class="text-end fw-semibold text-dark">v{{ $systemInfo['php_version'] }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="ps-0 py-3 text-muted"><i class="fas fa-database me-2 text-warning"></i> Driver Database</td>
                                    <td class="text-end fw-semibold text-dark text-uppercase">{{ $systemInfo['db_driver'] }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="ps-0 py-3 text-muted"><i class="fas fa-tools me-2 text-secondary"></i> Environment</td>
                                    <td class="text-end fw-semibold text-dark text-capitalize">{{ $systemInfo['env'] }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0 py-3 text-muted"><i class="fas fa-globe me-2 text-info"></i> Zona Waktu</td>
                                    <td class="text-end fw-semibold text-dark">{{ $systemInfo['timezone'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Accounting Config Card -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-calculator text-primary me-2"></i> Parameter Akuntansi Biaya
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Konfigurasi perhitungan persediaan dan produksi.</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 p-3 bg-light rounded-3" style="border-left: 4px solid #667eea;">
                        <h6 class="fw-bold mb-1"><i class="fas fa-truck-loading me-2 text-primary"></i> Metode Penilaian Persediaan</h6>
                        <p class="text-muted small mb-0">Sistem menggunakan metode **FIFO (First In First Out)** untuk menilai dan menghitung harga pokok bahan baku yang dikeluarkan untuk produksi.</p>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-3" style="border-left: 4px solid #764ba2;">
                        <h6 class="fw-bold mb-1"><i class="fas fa-cogs me-2 text-secondary"></i> Metode Pengumpulan Biaya</h6>
                        <p class="text-muted small mb-0">Sistem menggunakan metode **Job Order Costing (Biaya Berdasarkan Pesanan)** untuk mengumpulkan seluruh biaya produksi (BBB, BTKL, BOP) per pesanan barang.</p>
                    </div>

                    <div class="p-3 bg-light rounded-3" style="border-left: 4px solid #198754;">
                        <h6 class="fw-bold mb-1"><i class="fas fa-book me-2 text-success"></i> Status Integrasi Jurnal</h6>
                        <p class="text-muted small mb-0">Seluruh modul transaksi (Pemakaian Bahan, Absensi/Biaya Tenaga Kerja, BOP, & Penyelesaian Job) **terintegrasi secara real-time** dengan Jurnal Umum dan Buku Besar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Entity Statistics -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-chart-pie text-primary me-2"></i> Statistik Master Data
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Total catatan master yang terdaftar di database.</p>
                </div>
                <div class="card-body p-4">
                    <div class="row text-center">
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h3 class="fw-bold text-primary mb-1">{{ $systemInfo['counts']['akun'] }}</h3>
                                <small class="text-muted">Akun COA</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h3 class="fw-bold text-success mb-1">{{ $systemInfo['counts']['bahan_baku'] }}</h3>
                                <small class="text-muted">Bahan Baku</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h3 class="fw-bold text-info mb-1">{{ $systemInfo['counts']['tenaga_kerja'] }}</h3>
                                <small class="text-muted">Tenaga Kerja</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h3 class="fw-bold text-warning mb-1">{{ $systemInfo['counts']['produk'] }}</h3>
                                <small class="text-muted">Produk Jadi</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h3 class="fw-bold text-danger mb-1">{{ $systemInfo['counts']['job_order'] }}</h3>
                                <small class="text-muted">Total Job Order</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
