@extends('layouts.app')

@section('title', 'Detail Produk - ' . $produk->nama_produk)
@section('page-title', 'Detail Produk')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ $produk->nama_produk }}</h4>
            <p class="text-muted mb-0">{{ $produk->kode_produk }} • {!! $produk->tipe_badge !!}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('produk.edit', $produk->id_produk) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Produk & BOM
            </a>
            <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    {{-- Info Produk --}}
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0"><i class="fas fa-box text-primary me-2"></i>Informasi Produk</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" style="width:40%">Kode</td>
                        <td><strong>{{ $produk->kode_produk }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $produk->nama_produk }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tipe</td>
                        <td>{!! $produk->tipe_badge !!}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Satuan</td>
                        <td>{{ $produk->satuan_produk }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($produk->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-aktif</span>
                            @endif
                        </td>
                    </tr>
                    @if($produk->deskripsi)
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>{{ $produk->deskripsi }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- BOM Bahan Baku --}}
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-boxes text-success me-2"></i>Bill of Materials — Bahan Baku</h6>
                    <span class="badge bg-success">{{ $produk->bomBahan->count() }} bahan</span>
                </div>
            </div>
            <div class="card-body">
                @if($produk->bomBahan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Nama Bahan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produk->bomBahan as $i => $bom)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        @if($bom->bahanBaku)
                                            <code>{{ $bom->bahanBaku->kode_bahan }}</code>
                                        @else
                                            <code>{{ $bom->produkWip->kode_produk }}</code>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bom->bahanBaku)
                                            <strong>{{ $bom->bahanBaku->nama_bahan }}</strong> <span class="badge bg-primary ms-1">Bahan Baku</span>
                                        @else
                                            <strong>{{ $bom->produkWip->nama_produk }}</strong> <span class="badge bg-info text-dark ms-1">WIP</span>
                                        @endif
                                    </td>
                                    <td>{{ $bom->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-boxes fa-2x mb-2 d-block"></i>
                        Belum ada BOM bahan baku.<br>
                        <a href="{{ route('produk.edit', $produk->id_produk) }}" class="btn btn-sm btn-outline-success mt-2">
                            <i class="fas fa-plus me-1"></i>Tambah BOM
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- BOM Mesin --}}
<div class="card mb-4">
    <div class="card-header bg-white border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-cogs text-warning me-2"></i>Bill of Materials — Mesin & Peralatan</h6>
            <span class="badge bg-warning text-dark">{{ $produk->bomMesin->count() }} mesin</span>
        </div>
    </div>
    <div class="card-body">
        @if($produk->bomMesin->count() > 0)
            <div class="row">
                @foreach($produk->bomMesin as $mesin)
                <div class="col-md-3 mb-3">
                    <div class="card border-warning border-opacity-50">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 text-warning">
                                    <i class="fas fa-cog fa-lg"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $mesin->nama_mesin }}</div>
                                    @if($mesin->keterangan)
                                        <small class="text-muted">{{ $mesin->keterangan }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-3 text-muted">
                <i class="fas fa-cogs fa-2x mb-2 d-block"></i>
                Belum ada BOM mesin.
            </div>
        @endif
    </div>
</div>

{{-- Riwayat Job Order --}}
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0"><i class="fas fa-history text-info me-2"></i>Riwayat Job Order</h6>
    </div>
    <div class="card-body">
        @if($produk->permintaanProduksi->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor Job</th>
                            <th>Tanggal</th>
                            <th class="text-end">Jumlah</th>
                            <th>Status</th>
                            <th class="text-end">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk->permintaanProduksi->sortByDesc('tanggal_mulai')->take(10) as $job)
                        <tr>
                            <td>
                                <a href="{{ route('permintaan-produksi.show', $job->id_permintaan_produksi) }}">
                                    {{ $job->nomor_job }}
                                </a>
                            </td>
                            <td>{{ $job->tanggal_mulai->format('d/m/Y') }}</td>
                            <td class="text-end">{{ number_format($job->jumlah_produksi, 0) }} {{ $produk->satuan_produk }}</td>
                            <td>
                                @if($job->status == 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($job->status == 'proses')
                                    <span class="badge bg-primary">Proses</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($job->total_biaya_produksi, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                Belum ada job order untuk produk ini.
            </div>
        @endif
    </div>
</div>
@endsection
