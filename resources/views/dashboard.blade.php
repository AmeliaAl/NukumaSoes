@extends('layouts.app')

@section('title', 'Dashboard Produksi')
@section('page-title', 'Dashboard Produksi')

@section('content')
<div class="dashboard-simple">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Dashboard Produksi</h1>
            <p class="text-muted mb-0">Kondisi operasional produksi per {{ now()->translatedFormat('d F Y') }}</p>
        </div>
        <a href="{{ route('permintaan-produksi.index') }}" class="btn btn-primary px-3">
            <i class="fas fa-clipboard-list me-2"></i>Lihat Job Order
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="summary-card h-100">
                <div class="summary-icon text-primary bg-primary-subtle"><i class="fas fa-industry"></i></div>
                <div>
                    <div class="summary-label">Job Order Aktif</div>
                    <div class="summary-value">{{ $jobAktif }}</div>
                    <small class="text-muted">{{ $jobPending }} pending, {{ $jobProses }} proses</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="summary-card h-100">
                <div class="summary-icon text-info bg-info-subtle"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="summary-label">Batch Hari Ini</div>
                    <div class="summary-value">{{ $batchHariIni }}</div>
                    <small class="text-muted">Rencana atau sedang diproses</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="summary-card h-100">
                <div class="summary-icon text-success bg-success-subtle"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="summary-label">Biaya Bulan Ini</div>
                    <div class="summary-value summary-currency">Rp {{ number_format($totalBiayaBulanIni, 0, ',', '.') }}</div>
                    <small class="text-muted">Akumulasi biaya job order</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="summary-card h-100">
                <div class="summary-icon {{ $stokMenipis > 0 ? 'text-danger bg-danger-subtle' : 'text-success bg-success-subtle' }}">
                    <i class="fas {{ $stokMenipis > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check' }}"></i>
                </div>
                <div>
                    <div class="summary-label">Stok Perlu Perhatian</div>
                    <div class="summary-value">{{ $stokMenipis }}</div>
                    <small class="text-muted">Bahan di bawah stok minimum</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-3">
            <div class="panel-card h-100">
                <h2 class="h6 fw-bold mb-3">Status Job Order</h2>
                <div class="status-row">
                    <span><span class="status-dot bg-secondary"></span>Pending</span>
                    <strong>{{ $jobPending }}</strong>
                </div>
                <div class="status-row">
                    <span><span class="status-dot bg-primary"></span>Proses</span>
                    <strong>{{ $jobProses }}</strong>
                </div>
                <div class="status-row">
                    <span><span class="status-dot bg-success"></span>Selesai</span>
                    <strong>{{ $jobSelesai }}</strong>
                </div>
                <hr>
                <p class="small text-muted mb-0">Angka ini menunjukkan posisi seluruh job order produksi saat ini.</p>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h6 fw-bold mb-1">Job Order Terbaru</h2>
                        <p class="small text-muted mb-0">Enam job order yang terakhir dicatat.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 simple-table">
                        <thead>
                            <tr>
                                <th>Nomor Job</th>
                                <th>Produk</th>
                                <th class="text-end">Target</th>
                                <th>Status</th>
                                <th class="text-end">Biaya</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobTerbaru as $job)
                                @php
                                    $statusClass = match($job->status) {
                                        'proses' => 'text-bg-primary',
                                        'selesai' => 'text-bg-success',
                                        default => 'text-bg-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $job->nomor_job }}</div>
                                        <small class="text-muted">{{ $job->tanggal_mulai?->format('d/m/Y') ?? '-' }}</small>
                                    </td>
                                    <td>{{ $job->produk->nama_produk ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($job->jumlah_produksi, 2, ',', '.') }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ ucfirst($job->status) }}</span></td>
                                    <td class="text-end">Rp {{ number_format($job->total_biaya_produksi, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('permintaan-produksi.show', $job->id_permintaan_produksi) }}" class="btn btn-sm btn-light" title="Lihat detail">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">Belum ada job order produksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dashboard-simple { max-width: 1500px; margin: 0 auto; }
    .summary-card, .panel-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(16, 24, 40, .04);
    }
    .summary-card { display: flex; align-items: center; gap: 14px; padding: 18px; }
    .panel-card { padding: 20px; }
    .summary-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        flex: 0 0 42px;
    }
    .summary-label { color: #6c757d; font-size: .8rem; font-weight: 600; }
    .summary-value { color: #212529; font-size: 1.55rem; line-height: 1.2; font-weight: 700; }
    .summary-currency { font-size: 1.15rem; }
    .status-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f3f5; }
    .status-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 8px; }
    .simple-table thead th { color: #6c757d; font-size: .75rem; text-transform: uppercase; border-bottom-width: 1px; }
    .simple-table td { font-size: .875rem; border-color: #f1f3f5; }
    @media (max-width: 575.98px) {
        .panel-card { padding: 16px; }
        .summary-currency { font-size: 1rem; }
    }
</style>
@endpush
