@extends('layouts.app')

@section('title', 'Laporan Biaya Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📊 Laporan Biaya Produksi</h1>
            <p class="text-muted mb-0">Daftar job order yang sudah selesai diproduksi</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.biaya-produksi.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" 
                               class="form-control" 
                               name="tanggal_mulai" 
                               value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" 
                               class="form-control" 
                               name="tanggal_akhir" 
                               value="{{ request('tanggal_akhir') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Produk</label>
                        <select class="form-select" name="id_produk">
                            <option value="">Semua Produk</option>
                            @if(isset($produkList))
                                @foreach($produkList as $produk)
                                    <option value="{{ $produk->id_produk }}" 
                                            {{ request('id_produk') == $produk->id_produk ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if(isset($jobOrders) && $jobOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Job Order</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th class="text-end">Biaya Bahan</th>
                                <th class="text-end">Biaya TK</th>
                                <th class="text-end">Biaya Overhead</th>
                                <th class="text-end">Total Biaya</th>
                                <th class="text-end">HPP/Unit</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobOrders as $job)
                            <tr>
                                <td>
                                    <strong>{{ $job->nomor_job }}</strong>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($job->tanggal_mulai)->format('d/m/Y') }}</td>
                                <td>{{ $job->produk->nama_produk ?? '-' }}</td>
                                <td>{{ number_format($job->jumlah_produksi, 0, ',', '.') }} {{ $job->produk->satuan_produk ?? '' }}</td>
                                <td class="text-end">Rp {{ number_format($job->total_biaya_bahan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($job->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($job->total_biaya_overhead ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <strong>Rp {{ number_format($job->total_biaya_produksi ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-success">
                                        Rp {{ number_format($job->harga_pokok_per_unit ?? 0, 0, ',', '.') }} / {{ $job->produk->satuan_produk ?? 'Unit' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('laporan.biaya-produksi.show', $job->id_permintaan_produksi) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('laporan.biaya-produksi.kartu-biaya', $job->id_permintaan_produksi) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Kartu Biaya">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="{{ route('laporan.biaya-produksi.pdf', $job->id_permintaan_produksi) }}" 
                                           class="btn btn-sm btn-danger" 
                                           target="_blank"
                                           title="Export PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">TOTAL:</th>
                                <th>{{ number_format($jobOrders->sum('jumlah_produksi'), 0, ',', '.') }}</th>
                                <th class="text-end">Rp {{ number_format($jobOrders->sum('total_biaya_bahan'), 0, ',', '.') }}</th>
                                <th class="text-end">Rp {{ number_format($jobOrders->sum('total_biaya_tenaga_kerja'), 0, ',', '.') }}</th>
                                <th class="text-end">Rp {{ number_format($jobOrders->sum('total_biaya_overhead'), 0, ',', '.') }}</th>
                                <th class="text-end">
                                    <strong>Rp {{ number_format($jobOrders->sum('total_biaya_produksi'), 0, ',', '.') }}</strong>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $jobOrders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Data Laporan</h5>
                    <p class="text-muted">Job order yang sudah selesai akan muncul di sini</p>
                    <a href="{{ route('permintaan-produksi.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Buat Job Order
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection