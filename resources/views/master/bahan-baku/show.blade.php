@extends('layouts.app')

@section('title', 'Detail Bahan Baku')
@section('page-title', 'Detail Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Detail Bahan Baku: {{ $bahan->nama_bahan }}</h4>
            <p class="text-muted mb-0">Informasi lengkap bahan baku dan kartu stok FIFO</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bahan-baku.edit', $bahan->id_bahan) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Info Bahan Baku -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Bahan Baku</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">Kode Bahan</th>
                        <td><strong>{{ $bahan->kode_bahan }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nama Bahan</th>
                        <td><strong>{{ $bahan->nama_bahan }}</strong></td>
                    </tr>
                    <tr>
                        <th>Satuan Pakai (Terkecil)</th>
                        <td>{{ $bahan->satuan }}</td>
                    </tr>
                    @if($bahan->satuan_beli)
                    <tr>
                        <th>Satuan Beli (Kemasan)</th>
                        <td>{{ $bahan->satuan_beli }} (Isi: {{ floatval($bahan->isi_per_kemasan) }} {{ $bahan->satuan }})</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($bahan->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Informasi Stok</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">Stok Saat Ini</th>
                        <td>
                            <h5 class="text-primary mb-0">
                                {{ number_format($bahan->stok_saat_ini, 2) }} {{ $bahan->satuan }}
                            </h5>
                        </td>
                    </tr>
                    <tr>
                        <th>Stok Minimum</th>
                        <td>{{ number_format($bahan->stok_minimum, 2) }} {{ $bahan->satuan }}</td>
                    </tr>
                    <tr>
                        <th>Status Stok</th>
                        <td>
                            @if($bahan->stok_saat_ini <= $bahan->stok_minimum)
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Stok Menipis
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Stok Aman
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- FIFO Batches (Kartu Stok) -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Kartu Stok FIFO (Batches Tersedia)</h5>
    </div>
    <div class="card-body">
        @if($bahan->stokBahanBaku->where('status', 'tersedia')->count() > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Metode FIFO:</strong> Sistem akan mengambil stok dari batch yang paling lama masuk terlebih dahulu saat ada pemakaian bahan.
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="100">Batch ID</th>
                            <th>Tanggal Masuk</th>
                            <th>Nomor Penerimaan</th>
                            <th class="text-end">Jumlah Masuk</th>
                            <th class="text-end">Sisa Stok</th>
                            <th class="text-end">Terpakai</th>
                            <th class="text-end">Harga/Satuan</th>
                            <th class="text-end">Nilai Stok</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bahan->stokBahanBaku->where('status', 'tersedia')->sortBy('tanggal_masuk') as $stok)
                        <tr>
                            <td><span class="badge bg-secondary">#{{ $stok->id_stok }}</span></td>
                            <td>{{ $stok->tanggal_masuk->format('d/m/Y') }}</td>
                            <td>
                                @if($stok->penerimaanBahanBaku)
                                    <a href="{{ route('penerimaan-bahan-baku.show', $stok->id_penerimaan) }}">
                                        {{ $stok->penerimaanBahanBaku->nomor_penerimaan }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($stok->jumlah_masuk, 2) }}</td>
                            <td class="text-end">
                                <strong class="text-success">{{ number_format($stok->sisa_stok, 2) }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="text-danger">{{ number_format($stok->jumlah_masuk - $stok->sisa_stok, 2) }}</span>
                            </td>
                            <td class="text-end">Rp {{ number_format($stok->harga_per_satuan, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <strong class="text-primary">
                                    Rp {{ number_format($stok->sisa_stok * $stok->harga_per_satuan, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">Tersedia</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total Stok Tersedia:</th>
                            <th class="text-end">
                                {{ number_format($bahan->stokBahanBaku->where('status', 'tersedia')->sum('sisa_stok'), 2) }} {{ $bahan->satuan }}
                            </th>
                            <th colspan="2"></th>
                            <th class="text-end">
                                Rp {{ number_format($bahan->stokBahanBaku->where('status', 'tersedia')->sum(function($s) { return $s->sisa_stok * $s->harga_per_satuan; }), 0, ',', '.') }}
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-warning text-center">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p class="mb-0">Tidak ada batch stok tersedia. Lakukan penerimaan bahan baku untuk menambah stok.</p>
                <a href="{{ route('penerimaan-bahan-baku.create') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-plus me-2"></i>Terima Bahan Baku
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Riwayat Batch Habis -->
@if($bahan->stokBahanBaku->where('status', 'habis')->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Batch Habis</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Batch ID</th>
                        <th>Tanggal Masuk</th>
                        <th class="text-end">Jumlah Masuk</th>
                        <th class="text-end">Harga/Satuan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bahan->stokBahanBaku->where('status', 'habis')->sortByDesc('tanggal_masuk') as $stok)
                    <tr>
                        <td><span class="badge bg-secondary">#{{ $stok->id_stok }}</span></td>
                        <td>{{ $stok->tanggal_masuk->format('d/m/Y') }}</td>
                        <td class="text-end">{{ number_format($stok->jumlah_masuk, 2) }}</td>
                        <td class="text-end">Rp {{ number_format($stok->harga_per_satuan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">Habis</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Riwayat Pemakaian Terakhir -->
<div class="card">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Riwayat Pemakaian Terakhir (10 Transaksi)</h5>
    </div>
    <div class="card-body">
        @if($bahan->pemakaianBahanBaku->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Job Order</th>
                            <th>Dari Batch</th>
                            <th class="text-end">Jumlah Pakai</th>
                            <th class="text-end">Harga/Satuan</th>
                            <th class="text-end">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bahan->pemakaianBahanBaku->sortByDesc('created_at')->take(10) as $pakai)
                        <tr>
                            <td>{{ $pakai->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('permintaan-produksi.show', $pakai->id_permintaan_produksi) }}">
                                    {{ $pakai->permintaanProduksi->nomor_job }}
                                </a>
                            </td>
                            <td>
                                @if($pakai->stokBahanBaku)
                                    <span class="badge bg-info">#{{ $pakai->id_stok }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($pakai->jumlah_pakai, 2) }} {{ $bahan->satuan }}</td>
                            <td class="text-end">Rp {{ number_format($pakai->harga_per_satuan, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($pakai->total_biaya, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center mb-0">Belum ada riwayat pemakaian</p>
        @endif
    </div>
</div>
@endsection