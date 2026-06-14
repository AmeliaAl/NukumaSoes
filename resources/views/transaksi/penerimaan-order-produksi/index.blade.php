@extends('layouts.app')

@section('title', 'Penerimaan Order Produksi')

@section('content')
<div class="container-fluid">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-inbox me-2" style="color: #8e44ad;"></i>Penerimaan Order Produksi
            </h4>
            <p class="text-muted mb-0 small">Daftar order produksi yang diterima dari bagian penjualan/manajemen</p>
        </div>
        <a href="{{ route('penerimaan-order-produksi.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #8e44ad, #9b59b6); border: none; border-radius: 10px;">
            <i class="fas fa-plus me-2"></i>Terima Order Baru
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #d4edda, #c3e6cb);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Banner --}}
    <div class="alert border-0 mb-4" style="background: linear-gradient(135deg, #f3e5f5, #e8d5f5); border-radius: 12px;">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-info-circle fs-4" style="color: #8e44ad;"></i>
            <div>
                <strong style="color: #6c3483;">Tentang Modul Ini:</strong>
                <p class="mb-0 small text-muted mt-1">
                    Bagian ini digunakan ketika Anda <b>menerima kode order</b> dari departemen lain (Penjualan/Manajemen). 
                    Setiap kode order yang diterima akan otomatis membuat <b>Job Order</b> baru yang siap diproses oleh bagian produksi.
                </p>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
            <div class="d-flex align-items-center">
                <i class="fas fa-list text-white me-2"></i>
                <h6 class="text-white fw-bold mb-0">Daftar Order Diterima ({{ $orders->count() }})</h6>
            </div>
        </div>
        <div class="card-body p-0">
            @if($orders->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada order yang diterima</h6>
                    <p class="text-muted small">Klik tombol "Terima Order Baru" untuk mulai mencatat order dari departemen lain</p>
                    <a href="{{ route('penerimaan-order-produksi.create') }}" class="btn btn-sm btn-primary" style="background: #8e44ad; border: none;">
                        <i class="fas fa-plus me-1"></i>Terima Order Pertama
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th class="px-4 py-3 text-muted small fw-semibold">#</th>
                                <th class="py-3 text-muted small fw-semibold">KODE ORDER EKSTERNAL</th>
                                <th class="py-3 text-muted small fw-semibold">NAMA PEMESAN</th>
                                <th class="py-3 text-muted small fw-semibold">PRODUK</th>
                                <th class="py-3 text-muted small fw-semibold">JOB ORDER</th>
                                <th class="py-3 text-muted small fw-semibold">TGL TERIMA</th>
                                <th class="py-3 text-muted small fw-semibold">JUMLAH</th>
                                <th class="py-3 text-muted small fw-semibold">STATUS</th>
                                <th class="py-3 text-muted small fw-semibold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $i => $order)
                            <tr>
                                <td class="px-4 py-3 text-muted small">{{ $i + 1 }}</td>
                                <td class="py-3">
                                    <span class="badge fw-semibold px-3 py-2" style="background: #f3e5f5; color: #6c3483; font-size: 13px; border-radius: 8px;">
                                        <i class="fas fa-barcode me-1"></i>{{ $order->kode_order_eksternal }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold small">{{ $order->nama_pemesan ?? $order->customer ?? '-' }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold small">{{ $order->produk->nama_produk ?? '-' }}</div>
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('permintaan-produksi.show', $order->id_permintaan_produksi) }}" class="text-decoration-none fw-semibold small" style="color: #8e44ad;">
                                        {{ $order->nomor_job }}
                                    </a>
                                </td>
                                <td class="py-3 small text-muted">
                                    {{ $order->tanggal_terima_order ? \Carbon\Carbon::parse($order->tanggal_terima_order)->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3 small">
                                    {{ number_format($order->jumlah_produksi, 0, ',', '.') }} unit
                                </td>
                                <td class="py-3">
                                    @if($order->status === 'pending')
                                        <span class="badge" style="background: #fff3cd; color: #856404; border-radius: 8px;">Pending</span>
                                    @elseif($order->status === 'proses')
                                        <span class="badge" style="background: #cce5ff; color: #004085; border-radius: 8px;">Proses</span>
                                    @else
                                        <span class="badge" style="background: #d4edda; color: #155724; border-radius: 8px;">Selesai</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('permintaan-produksi.show', $order->id_permintaan_produksi) }}" 
                                       class="btn btn-sm" 
                                       style="background: #8e44ad; color: white; border-radius: 8px; font-size: 12px;"
                                       title="Lihat Job Order">
                                        <i class="fas fa-eye me-1"></i>Lihat Job
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
