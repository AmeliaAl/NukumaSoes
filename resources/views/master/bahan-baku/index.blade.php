@extends('layouts.app')

@section('title', 'Bahan Baku')
@section('page-title', 'Data Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Data Bahan Baku</h4>
            <p class="text-muted mb-0">Kelola data bahan baku produksi</p>
        </div>
        <a href="{{ route('bahan-baku.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Bahan Baku
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

@php
    $stokMenipisBahan = $bahanBaku->filter(fn($b) => $b->stok_saat_ini <= $b->stok_minimum && $b->status == 'aktif');
@endphp

@if($stokMenipisBahan->count() > 0)
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
            </div>
            <div class="flex-grow-1">
                <strong>⚠️ Peringatan Stok Menipis!</strong>
                <p class="mb-2">{{ $stokMenipisBahan->count() }} bahan baku di bawah stok minimum:</p>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($stokMenipisBahan as $b)
                        <span class="badge bg-danger fs-6 py-2 px-3">
                            {{ $b->nama_bahan }}:
                            {{ number_format($b->stok_saat_ini, 0) }}/{{ number_format($b->stok_minimum, 0) }} {{ $b->satuan }}
                        </span>
                    @endforeach
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="bahanBakuTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Bahan</th>
                        <th>Jenis</th>
                        <th>Satuan</th>
                        <th class="text-end">Stok Saat Ini</th>
                        <th class="text-end">Stok Minimum</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bahanBaku as $bahan)
                    <tr>
                        <td><strong>{{ $bahan->kode_bahan }}</strong></td>
                        <td>{{ $bahan->nama_bahan }}</td>
                        <td>
                            @if($bahan->jenis_bahan == 'langsung')
                                <span class="badge bg-primary">Langsung</span>
                            @else
                                <span class="badge bg-warning text-dark">Tidak Langsung / Kemasan</span>
                            @endif
                        </td>
                        <td>
                            {{ $bahan->satuan }}
                            @if($bahan->satuan_beli)
                                <br><small class="text-muted">1 {{ $bahan->satuan_beli }} = {{ floatval($bahan->isi_per_kemasan) }} {{ $bahan->satuan }}</small>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($bahan->stok_saat_ini <= $bahan->stok_minimum)
                                <span class="badge bg-danger">{{ number_format($bahan->stok_saat_ini, 0) }}</span>
                            @else
                                <span class="badge bg-success">{{ number_format($bahan->stok_saat_ini, 0) }}</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($bahan->stok_minimum, 0) }}</td>
                        <td>
                            @if($bahan->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('bahan-baku.show', $bahan->id_bahan) }}" 
                                   class="btn btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('bahan-baku.edit', $bahan->id_bahan) }}" 
                                   class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmDelete({{ $bahan->id_bahan }})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#bahanBakuTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25
        });
    });
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Bahan Baku?',
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
                form.action = '/bahan-baku/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection