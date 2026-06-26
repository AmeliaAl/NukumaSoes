@extends('layouts.app')

@section('title', 'Kategori BOP')
@section('page-title', 'Data Kategori BOP')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Data Kategori BOP</h4>
            <p class="text-muted mb-0">Kelola kategori Biaya Overhead Pabrik (BOP) dinamis</p>
        </div>
        <a href="{{ route('kategori-bop.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Kategori BOP
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

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="kategoriBopTable" class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 80px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                        <th>Akun COA</th>
                        <th class="text-center" style="width: 150px;">Total Penggunaan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategoriBop as $index => $kategori)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $kategori->nama_kategori }}</strong></td>
                        <td>{{ $kategori->keterangan ?? '-' }}</td>
                        <td>
                            @if($kategori->akun)
                                <span class="badge bg-secondary">{{ $kategori->akun->kode_akun }}</span>
                                {{ $kategori->akun->nama_akun }}
                            @else
                                <span class="text-danger">Belum dipetakan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $kategori->biaya_overhead_pabrik_count }} Kali</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('kategori-bop.edit', $kategori->id_kategori_bop) }}" 
                                   class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmDelete({{ $kategori->id_kategori_bop }}, '{{ $kategori->nama_kategori }}')" 
                                        title="Hapus"
                                        {{ $kategori->biaya_overhead_pabrik_count > 0 ? 'disabled' : '' }}>
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
        $('#kategoriBopTable').DataTable({
            order: [[1, 'asc']],
            pageLength: 25
        });
    });
    
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Kategori BOP?',
            text: `Apakah Anda yakin ingin menghapus kategori "${name}"? Data yang sudah dihapus tidak dapat dikembalikan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/kategori-bop/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection
