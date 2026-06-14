@extends('layouts.app')

@section('title', 'Detail Permintaan Bahan')
@section('page-title', 'Detail Permintaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Detail Permintaan: {{ $permintaan->nomor_permintaan }}</h4>
            <p class="text-muted mb-0">Informasi lengkap permintaan bahan baku</p>
        </div>
        <div class="d-flex gap-2">
            @if($permintaan->status_penerimaan !== 'completed')
                <a href="{{ route('penerimaan-bahan-baku.create') }}?permintaan={{ $permintaan->nomor_permintaan }}" 
                   class="btn btn-primary">
                    <i class="fas fa-box me-2"></i>
                    @if($permintaan->status_penerimaan === 'partial')
                        Terima Lagi
                    @else
                        Terima Bahan
                    @endif
                </a>
            @endif
            
            <a href="{{ route('permintaan-bahan-baku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Permintaan</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">Nomor Permintaan</th>
                        <td><strong>{{ $permintaan->nomor_permintaan }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal Permintaan</th>
                        <td>{{ $permintaan->tanggal_permintaan->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>{{ $permintaan->admin->name ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @php $sp = $permintaan->status_penerimaan; @endphp
                            @if($sp === 'completed')
                                <span class="badge bg-success">Selesai Diterima</span>
                            @elseif($sp === 'partial')
                                <span class="badge bg-warning text-dark">Sebagian Diterima</span>
                            @else
                                <span class="badge bg-secondary">Belum Diterima</span>
                            @endif
                        </td>
                    </tr>
                    @if($permintaan->keperluan)
                    <tr>
                        <th>Keperluan</th>
                        <td>{{ $permintaan->keperluan }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $permintaan->keterangan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 mt-3">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Daftar Bahan Baku</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th class="text-end">Jumlah Permintaan</th>
                                <th class="text-end">Jumlah Diterima</th>
                                <th class="text-end">Stok Saat Ini</th>
                                <th class="text-center">Status Penerimaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permintaan->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->bahanBaku->kode_bahan }}</td>
                                <td>{{ $detail->bahanBaku->nama_bahan }}</td>
                                <td class="text-end">
                                    <h6 class="text-primary mb-0">
                                        {{ number_format($detail->jumlah_permintaan, 2) }} {{ $detail->bahanBaku->satuan }}
                                    </h6>
                                </td>
                                <td class="text-end">
                                    {{ number_format($detail->jumlah_diterima, 2) }} {{ $detail->bahanBaku->satuan }}
                                </td>
                                <td class="text-end">
                                    @if($detail->bahanBaku->stok_saat_ini <= $detail->bahanBaku->stok_minimum)
                                        <span class="badge bg-danger">
                                            {{ number_format($detail->bahanBaku->stok_saat_ini, 2) }} {{ $detail->bahanBaku->satuan }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            {{ number_format($detail->bahanBaku->stok_saat_ini, 2) }} {{ $detail->bahanBaku->satuan }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($detail->status_penerimaan === 'completed')
                                        <span class="badge bg-success">Complete</span>
                                    @elseif($detail->status_penerimaan === 'partial')
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    @else
                                        <span class="badge bg-secondary">Belum</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($permintaan->penerimaanBahanBaku->count() > 0)
<div class="card mt-3">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Penerimaan Bahan Baku ({{ $permintaan->penerimaanBahanBaku->count() }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No. Penerimaan</th>
                        <th>Tanggal</th>
                        <th class="text-center">Jumlah Item</th>
                        <th class="text-end">Total Biaya</th>
                        <th>Supplier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permintaan->penerimaanBahanBaku as $penerimaan)
                    <tr>
                        <td><strong>{{ $penerimaan->nomor_penerimaan }}</strong></td>
                        <td>{{ $penerimaan->tanggal_penerimaan->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $penerimaan->details->count() }} Item</td>
                        <td class="text-end"><strong class="text-success">Rp {{ number_format($penerimaan->total_biaya, 0, ',', '.') }}</strong></td>
                        <td>{{ $penerimaan->supplier ?? '-' }}</td>
                        <td>
                            <a href="{{ route('penerimaan-bahan-baku.show', $penerimaan->id_penerimaan) }}" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
    @if($permintaan->status_penerimaan !== 'completed')
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Belum selesai diterima.</strong> Segera lakukan penerimaan bahan baku untuk update stok.
            <a href="{{ route('penerimaan-bahan-baku.create') }}?permintaan={{ $permintaan->nomor_permintaan }}" 
               class="btn btn-sm btn-primary ms-3">
                <i class="fas fa-box me-2"></i>Terima Sekarang
            </a>
        </div>
    @endif
@endif
@endsection