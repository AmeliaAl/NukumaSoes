@extends('layouts.app')

@section('title', 'Kartu Stok Bahan Baku')
@section('page-title', 'Kartu Stok Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Kartu Stok: {{ $bahan->nama_bahan }}</h4>
            <p class="text-muted mb-0">{{ $bahan->kode_bahan }} - Saldo berjalan bahan baku berdasarkan transaksi masuk dan keluar.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bahan-baku.show', $bahan->id_bahan) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>Detail Bahan
            </a>
            <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="p-3 bg-light rounded">
                    <div class="text-muted small">Satuan</div>
                    <div class="fw-bold">{{ $bahan->satuan }}</div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="p-3 bg-light rounded">
                    <div class="text-muted small">Stok Saat Ini</div>
                    <div class="fw-bold text-primary">{{ number_format($bahan->stok_saat_ini, 2, ',', '.') }} {{ $bahan->satuan }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded">
                    <div class="text-muted small">Stok Minimum</div>
                    <div class="fw-bold">{{ number_format($bahan->stok_minimum, 2, ',', '.') }} {{ $bahan->satuan }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Riwayat Kartu Stok</h6>
    </div>
    <div class="card-body">
        @if($kartuStok->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                            <th class="text-end">Harga/Satuan</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kartuStok as $item)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                                <td class="text-end">
                                    @if($item->jenis === 'masuk')
                                        <span class="text-success fw-semibold">{{ number_format($item->jumlah, 2, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->jenis === 'keluar')
                                        <span class="text-danger fw-semibold">{{ number_format($item->jumlah, 2, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->harga_per_satuan, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($item->saldo, 2, ',', '.') }} {{ $bahan->satuan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                Belum ada transaksi stok untuk bahan baku ini.
            </div>
        @endif
    </div>
</div>
@endsection
