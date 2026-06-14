@extends('layouts.app')

@section('title', 'Detail Stok Produk')
@section('page-title', 'Detail Stok Produk')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Detail Stok: {{ $stok->produk->nama_produk ?? '-' }}</h4>
            <p class="text-muted mb-0">{!! $stok->tipe_badge !!}</p>
        </div>
        <a href="{{ route('stok-produk.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Stok</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">ID Stok</th>
                        <td>: #{{ $stok->id_stok_produk }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <td>: {{ $stok->tanggal_masuk->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Nama Produk</th>
                        <td>: <strong>{{ $stok->produk->nama_produk ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tipe Stok</th>
                        <td>: {!! $stok->tipe_badge !!} ({{ $stok->tipe_label }})</td>
                    </tr>
                    <tr>
                        <th>Sumber (Job Order)</th>
                        <td>: 
                            @if($stok->permintaanProduksi)
                                <a href="{{ route('permintaan-produksi.show', $stok->id_permintaan_produksi) }}" class="text-decoration-none">
                                    {{ $stok->permintaanProduksi->nomor_job }}
                                </a>
                            @else
                                <span class="text-muted">Manual / Tidak ada data</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Kuantitas</th>
                        <td>: <span class="fs-5">{{ number_format($stok->jumlah, 2, ',', '.') }} {{ $stok->satuan }}</span></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            @if($stok->status == 'tersedia')
                                <span class="badge bg-success">Tersedia</span>
                            @elseif($stok->status == 'terjual')
                                <span class="badge bg-info">Terjual / Dipakai</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($stok->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>: {{ $stok->keterangan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Nilai Stok</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase mb-2">Harga Pokok Produksi (HPP) / Unit</h6>
                    <h3 class="text-dark mb-0">Rp {{ number_format($stok->harga_pokok_per_unit, 0, ',', '.') }}</h3>
                </div>
                <hr>
                <div>
                    <h6 class="text-muted text-uppercase mb-2">Total Nilai Stok</h6>
                    <h2 class="text-success mb-0">Rp {{ number_format($stok->total_nilai, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
