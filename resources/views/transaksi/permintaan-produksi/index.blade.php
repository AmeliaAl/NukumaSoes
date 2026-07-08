@extends('layouts.app')

@section('title', 'Job Order')
@section('page-title', 'Job Order Produksi')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Job Order Produksi</h4>
            <p class="text-muted mb-0">Kelola pesanan produksi dengan metode Job Order Costing</p>
        </div>
        <a href="{{ route('permintaan-produksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat Job Order Baru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filter Status -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="proses">Proses</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterProduk">
                    <option value="">Semua Produk</option>
                    @foreach($produkList as $produk)
                        <option value="{{ $produk->id_produk }}">{{ $produk->nama_produk }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="jobOrderTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Nomor Job</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th class="text-end">Jumlah</th>
                        <th>Status</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-end">Biaya/Unit</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobOrders as $job)
                    <tr>
                        <td>
                            <strong>{{ $job->nomor_job }}</strong>
                            <br>
                            <small class="text-muted">{{ $job->customer ?? '-' }}</small>
                        </td>
                        <td>
                            {{ $job->tanggal_mulai->format('d/m/Y') }}
                            @if($job->tanggal_selesai)
                                <br><small class="text-muted">Selesai: {{ $job->tanggal_selesai->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>{{ $job->produk->nama_produk }}</td>
                        <td class="text-end">
                            <strong>{{ number_format($job->jumlah_produksi, 0) }}</strong> {{ $job->produk->satuan_produk }}
                        </td>
                        <td>
                            @if($job->status == 'pending')
                                <span class="badge bg-secondary">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @elseif($job->status == 'proses')
                                <span class="badge bg-primary">
                                    <i class="fas fa-spinner"></i> Proses
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <strong class="text-primary">Rp {{ number_format($job->total_biaya_produksi ?? 0, 0, ',', '.') }}</strong>
                            <br>
                            <small class="text-muted">
                                BB: Rp {{ number_format($job->total_biaya_bahan ?? 0, 0, ',', '.') }}<br>
                                TK: Rp {{ number_format($job->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}<br>
                                OH: Rp {{ number_format($job->total_biaya_overhead ?? 0, 0, ',', '.') }}
                            </small>
                        </td>
                        <td class="text-end">
                            <strong>Rp {{ number_format($job->harga_pokok_per_unit ?? 0, 0, ',', '.') }}</strong>
                            <br>
                            <small class="text-muted">/ {{ $job->produk->satuan_produk ?? 'Unit' }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('permintaan-produksi.show', $job->id_permintaan_produksi) }}" 
                                   class="btn btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($job->status != 'selesai')
                                    <a href="{{ route('permintaan-produksi.edit', $job->id_permintaan_produksi) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($job->status == 'proses')
                                        <button type="button" class="btn btn-success" 
                                                onclick="completeJob({{ $job->id_permintaan_produksi }})" 
                                                title="Selesaikan">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                @endif
                                
                                @if($job->status == 'pending' || ($job->status == 'proses' && $job->pemakaianBahanBaku->count() == 0))
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDelete({{ $job->id_permintaan_produksi }})" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form Complete Job -->
<form id="completeForm" method="POST" style="display: none;">
    @csrf
</form>

<!-- Form Delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#jobOrderTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25
        });
        
        // Filter by status
        $('#filterStatus').on('change', function() {
            table.column(4).search(this.value).draw();
        });
        
        // Filter by produk
        $('#filterProduk').on('change', function() {
            table.column(2).search(this.value).draw();
        });
    });
    
    function completeJob(id) {
        Swal.fire({
            title: 'Selesaikan Job Order?',
            text: "Job order akan ditandai selesai dan biaya akan dikalkulasi final!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesaikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('completeForm');
                form.action = '/permintaan-produksi/' + id + '/complete';
                form.submit();
            }
        });
    }
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Job Order?',
            text: "Data yang sudah dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/permintaan-produksi/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection