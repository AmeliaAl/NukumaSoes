@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
@php
    // Persiapkan nama bulan Indonesia
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $chartJobLabels = [];
    $chartJobData = [];
    foreach($jobPerBulan as $item) {
        $chartJobLabels[] = ($namaBulan[$item->bulan] ?? '') . ' ' . $item->tahun;
        $chartJobData[] = $item->total;
    }

    $chartTopLabels = [];
    $chartTopData = [];
    foreach($topProduk as $item) {
        $chartTopLabels[] = $item->produk->nama_produk ?? 'Tidak Diketahui';
        $chartTopData[] = $item->total_produksi;
    }
@endphp

<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header border-0 mb-4 p-4 shadow-sm" style="border-radius: 16px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-1 text-dark">Dashboard Overview</h3>
                <p class="text-muted mb-0">Selamat datang kembali di sistem akuntansi biaya produksi **Nukuma Cantique**.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge px-3 py-2 bg-primary bg-opacity-10 text-primary fw-semibold" style="font-size: 13px; border-radius: 20px;">
                    <i class="fas fa-calendar-day me-2"></i>{{ now()->format('d F Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Metrik Utama Grid -->
    <div class="row mb-4">
        <!-- Highlight Card: Total Biaya Bulan Ini -->
        <div class="col-xl-4 col-lg-5 mb-3">
            <div class="card border-0 kpi-card p-4 h-100 d-flex flex-column justify-content-between" style="border-radius: 16px;">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-white-50 fw-semibold text-uppercase tracking-wider" style="font-size: 12px;">Biaya Produksi</span>
                        <div class="bg-white bg-opacity-20 text-white rounded-3 p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-wallet fs-5"></i>
                        </div>
                    </div>
                    <p class="text-white-50 mb-1" style="font-size: 14px;">Total Biaya Produksi (Bulan Ini)</p>
                    <h2 class="fw-bold text-white mb-2">Rp {{ number_format($totalBiayaBulanIni, 0, ',', '.') }}</h2>
                </div>
                <div class="mt-4">
                    <div class="d-flex align-items-center bg-white bg-opacity-10 rounded-3 p-2 text-white-50" style="font-size: 13px;">
                        <i class="fas fa-info-circle me-2 text-white"></i>
                        <span>Akumulasi real-time seluruh pesanan selesai bulan ini.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2x2 Grid untuk Stat Lainnya -->
        <div class="col-xl-8 col-lg-7">
            <div class="row h-100">
                <!-- Bahan Baku -->
                <div class="col-md-6 mb-3">
                    <div class="card glass-card border-0 p-3 h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1" style="font-size: 14px;">Bahan Baku</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalBahanBaku }}</h3>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold" style="border-radius: 20px;">
                                <i class="fas fa-check-circle me-1"></i> {{ $totalBahanBaku }} Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tenaga Kerja -->
                <div class="col-md-6 mb-3">
                    <div class="card glass-card border-0 p-3 h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1" style="font-size: 14px;">Tenaga Kerja</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalTenagaKerja }}</h3>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold" style="border-radius: 20px;">
                                <i class="fas fa-check-circle me-1"></i> {{ $totalTenagaKerja }} Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Produk Jadi -->
                <div class="col-md-6 mb-3">
                    <div class="card glass-card border-0 p-3 h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1" style="font-size: 14px;">Produk Jadi</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalProduk }}</h3>
                            </div>
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold" style="border-radius: 20px;">
                                <i class="fas fa-check-circle me-1"></i> {{ $totalProduk }} Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Job Order -->
                <div class="col-md-6 mb-3">
                    <div class="card glass-card border-0 p-3 h-100 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1" style="font-size: 14px;">Job Order</p>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalJob }}</h3>
                            </div>
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-cogs"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold" style="border-radius: 20px;">
                                <i class="fas fa-history me-1"></i> Akumulasi Total
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Pekerjaan Dinamis (Mini Banner) -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card glass-card border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Pesanan Pending</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $jobPending }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card glass-card border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Pesanan Diproses</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $jobProses }}</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card glass-card border-0 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Pesanan Selesai</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $jobSelesai }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Visualisasi Data -->
    <div class="row mb-4">
        <!-- Grafik Tren Job Order -->
        <div class="col-lg-8 mb-4">
            <div class="card glass-card border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Tren Job Order</h5>
                        <p class="text-muted small mb-0">Frekuensi pembukaan pesanan 6 bulan terakhir.</p>
                    </div>
                    <span class="badge bg-light text-muted border px-2 py-1"><i class="fas fa-chart-line me-1 text-primary"></i> Line Chart</span>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="jobOrderChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Top Produk -->
        <div class="col-lg-4 mb-4">
            <div class="card glass-card border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Produk Terlaris</h5>
                        <p class="text-muted small mb-0">Berdasarkan volume produksi (Selesai).</p>
                    </div>
                    <span class="badge bg-light text-muted border px-2 py-1"><i class="fas fa-chart-pie me-1 text-success"></i> Doughnut</span>
                </div>
                <div style="height: 300px; position: relative; display: flex; align-items: center; justify-content: center;">
                    <canvas id="topProdukChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Antrean & Peringatan Bahan -->
    <div class="row mb-4">
        <!-- Stok Menipis Alert -->
        <div class="col-lg-6 mb-4">
            <div class="card glass-card border-0 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i> Stok Menipis
                    </h5>
                    <span class="badge bg-danger rounded-pill px-2.5 py-1.5">{{ $stokMenipis->count() }}</span>
                </div>
                <div class="card-body px-4 py-3">
                    @if($stokMenipis->count() > 0)
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small" style="border-bottom: 2px solid #f0f0f0;">
                                        <th class="ps-0">Bahan Baku</th>
                                        <th class="text-end">Stok Sekarang</th>
                                        <th class="text-end">Stok Minimum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stokMenipis as $bahan)
                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                        <td class="ps-0 py-3">
                                            <div class="fw-bold text-dark">{{ $bahan->nama_bahan }}</div>
                                            <small class="text-muted text-uppercase">{{ $bahan->kode_bahan }}</small>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold px-2 py-1.5">
                                                {{ number_format($bahan->stok_saat_ini, 0) }} {{ $bahan->satuan }}
                                            </span>
                                        </td>
                                        <td class="text-end text-muted small">
                                            {{ number_format($bahan->stok_minimum, 0) }} {{ $bahan->satuan }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-inline-flex mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                                <i class="fas fa-check-circle fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Stok Bahan Baku Aman!</h6>
                            <p class="small mb-0">Semua kuantitas bahan baku berada di atas batas minimum.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Permintaan Bahan Baku Pending -->
        <div class="col-lg-6 mb-4">
            <div class="card glass-card border-0 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-clock text-warning me-2"></i> Permintaan Pending
                    </h5>
                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1.5">{{ $permintaanPending->count() }}</span>
                </div>
                <div class="card-body px-4 py-3">
                    @if($permintaanPending->count() > 0)
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small" style="border-bottom: 2px solid #f0f0f0;">
                                        <th class="ps-0">No. Permintaan</th>
                                        <th>Bahan Baku</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permintaanPending as $permintaan)
                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                        <td class="ps-0 py-3">
                                            <div class="fw-bold text-dark">{{ $permintaan->nomor_permintaan }}</div>
                                            <small class="text-muted">{{ $permintaan->tanggal_permintaan->format('d/m/Y') }}</small>
                                        </td>
                                        <td>{{ $permintaan->bahanBaku->nama_bahan ?? 'N/A' }}</td>
                                        <td class="text-end fw-semibold text-dark">
                                            {{ number_format($permintaan->jumlah_permintaan, 0) }} {{ $permintaan->bahanBaku->satuan ?? '' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="bg-light rounded-circle p-3 d-inline-flex mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                                <i class="fas fa-inbox fs-3 text-secondary"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Antrean Bersih</h6>
                            <p class="small mb-0">Tidak ada permintaan bahan baku yang berstatus pending.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Job Order Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card glass-card border-0">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-clipboard-list text-primary me-2"></i> Job Order Terbaru
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Status dan pembebanan biaya dari 5 pesanan teranyar.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($jobTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small" style="border-bottom: 2px solid #f0f0f0;">
                                        <th class="ps-0">Nomor Job</th>
                                        <th>Produk</th>
                                        <th>Jumlah Produksi</th>
                                        <th>Status</th>
                                        <th class="text-end">Total Biaya Produksi</th>
                                        <th class="text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobTerbaru as $job)
                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                        <td class="ps-0 py-3">
                                            <div class="fw-bold text-dark">{{ $job->nomor_job }}</div>
                                            <small class="text-muted">{{ $job->tanggal_mulai->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $job->produk->nama_produk ?? 'N/A' }}</td>
                                        <td>{{ number_format($job->jumlah_produksi, 0) }} {{ $job->produk->satuan_produk ?? '' }}</td>
                                        <td>
                                            @if($job->status == 'pending')
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold px-2.5 py-1.5" style="border-radius: 12px;">Pending</span>
                                            @elseif($job->status == 'proses')
                                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2.5 py-1.5" style="border-radius: 12px;">Proses</span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-2.5 py-1.5" style="border-radius: 12px;">Selesai</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            Rp {{ number_format($job->total_biaya_produksi, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('permintaan-produksi.show', $job->id_permintaan_produksi) }}" 
                                               class="btn btn-sm btn-outline-primary border-0 rounded-circle" 
                                               style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                               data-bs-toggle="tooltip" 
                                               title="Lihat Rincian Job">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="bg-light rounded-circle p-3 d-inline-flex mb-3" style="width: 60px; height: 60px; align-items: center; justify-content: center;">
                                <i class="fas fa-folder-open fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Belum Ada Job Order</h6>
                            <p class="small mb-0">Klik menu transaksi untuk membuat dokumen produksi pertama.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dashboard-container {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }

    .kpi-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.35);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.5);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }


</style>
@endpush
@endsection

@section('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    document.addEventListener("DOMContentLoaded", function () {
        // ==========================================
        // CHART 1: TREN JOB ORDER PER BULAN
        // ==========================================
        const ctxJob = document.getElementById('jobOrderChart').getContext('2d');
        
        // Buat gradien untuk background chart
        const jobGradient = ctxJob.createLinearGradient(0, 0, 0, 300);
        jobGradient.addColorStop(0, 'rgba(102, 126, 234, 0.4)');
        jobGradient.addColorStop(1, 'rgba(102, 126, 234, 0.0)');

        new Chart(ctxJob, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartJobLabels) !!},
                datasets: [{
                    label: 'Job Order Dibuka',
                    data: {!! json_encode($chartJobData) !!},
                    borderColor: '#667eea',
                    borderWidth: 3,
                    backgroundColor: jobGradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 8,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: {
                            family: 'Segoe UI',
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Segoe UI'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Segoe UI',
                                size: 11
                            },
                            color: '#888'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            beginAtZero: true,
                            font: {
                                family: 'Segoe UI',
                                size: 11
                            },
                            color: '#888'
                        }
                    }
                }
            }
        });

        // ==========================================
        // CHART 2: TOP 5 PRODUK TERLARIS (DOUGHNUT)
        // ==========================================
        const ctxTop = document.getElementById('topProdukChart').getContext('2d');
        
        new Chart(ctxTop, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartTopLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartTopData) !!},
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#198754',
                        '#0dcaf0',
                        '#ffc107'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                family: 'Segoe UI',
                                size: 11
                            },
                            color: '#555'
                        }
                    },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 8,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return ' ' + label + ': ' + value + ' unit';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection