@extends('layouts.app')

@section('title', 'Detail Penerimaan Bahan')
@section('page-title', 'Detail Penerimaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Detail Penerimaan: {{ $penerimaan->nomor_penerimaan }}</h4>
            <p class="text-muted mb-0">Informasi lengkap penerimaan bahan baku</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('penerimaan-bahan-baku.edit', $penerimaan->id_penerimaan) }}" 
               class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('penerimaan-bahan-baku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Penerimaan</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">Nomor Penerimaan</th>
                        <td><strong>{{ $penerimaan->nomor_penerimaan }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal Penerimaan</th>
                        <td>{{ $penerimaan->tanggal_penerimaan->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>{{ $penerimaan->supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Diterima Oleh</th>
                        <td>{{ $penerimaan->admin->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <th>Dari Permintaan</th>
                        <td>
                            @if($penerimaan->permintaanBahanBaku)
                                <a href="{{ route('permintaan-bahan-baku.show', $penerimaan->id_permintaan_bahan) }}">
                                    {{ $penerimaan->permintaanBahanBaku->nomor_permintaan }}
                                </a>
                            @else
                                <span class="text-muted">Penerimaan Langsung</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $penerimaan->keterangan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 mt-3">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Detail Bahan & Biaya</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th class="text-end">Jumlah Diterima</th>
                                <th class="text-end">Harga Per Satuan</th>
                                <th class="text-end">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penerimaan->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->bahanBaku->kode_bahan }}</td>
                                <td>{{ $detail->bahanBaku->nama_bahan }}</td>
                                <td class="text-end">
                                    {{ number_format($detail->jumlah_diterima, 2) }} {{ $detail->bahanBaku->satuan }}
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($detail->harga_per_satuan, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($detail->total_biaya, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Total Keseluruhan Biaya</th>
                                <th class="text-end fw-bold text-success fs-5">
                                    Rp {{ number_format($penerimaan->total_biaya, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FIFO Batch Info -->
<div class="card mt-3">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>FIFO Batch Information</h5>
    </div>
    <div class="card-body">
        @if($penerimaan->stokBahanBaku->count() > 0)
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Batch FIFO sudah dibuat untuk semua bahan baku!</strong>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Bahan Baku</th>
                            <th>Batch ID</th>
                            <th>Tanggal Masuk</th>
                            <th class="text-end">Jumlah Masuk</th>
                            <th class="text-end">Sisa Stok</th>
                            <th class="text-end">Harga/Satuan</th>
                            <th class="text-end">Nilai Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penerimaan->stokBahanBaku as $stok)
                        <tr>
                            <td>
                                <strong>{{ $stok->bahanBaku->nama_bahan }}</strong><br>
                                <small class="text-muted">{{ $stok->bahanBaku->kode_bahan }}</small>
                            </td>
                            <td><span class="badge bg-secondary">#{{ $stok->id_stok }}</span></td>
                            <td>{{ $stok->tanggal_masuk->format('d/m/Y H:i') }}</td>
                            <td class="text-end">{{ number_format($stok->jumlah_masuk, 2) }}</td>
                            <td class="text-end">
                                <strong>{{ number_format($stok->sisa_stok, 2) }}</strong>
                            </td>
                            <td class="text-end">Rp {{ number_format($stok->harga_per_satuan, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <strong class="text-success">
                                    Rp {{ number_format($stok->sisa_stok * $stok->harga_per_satuan, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                @if($stok->status == 'tersedia')
                                    <span class="badge bg-success">Tersedia</span>
                                @else
                                    <span class="badge bg-secondary">Habis</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Metode FIFO:</strong> Batch ini akan digunakan saat pemakaian bahan baku. Sistem akan mengambil dari batch yang paling lama masuk terlebih dahulu.
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Batch FIFO belum terbuat. Data mungkin belum tersinkronisasi.
            </div>
        @endif
    </div>
</div>
@endsection