@extends('layouts.app')

@section('title', 'Tenaga Kerja')
@section('page-title', 'Data Tenaga Kerja')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Data Tenaga Kerja</h4>
            <p class="text-muted mb-0">Kelola data tenaga kerja produksi</p>
        </div>
        <a href="{{ route('tenaga-kerja.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Tenaga Kerja
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
            <table id="tenagaKerjaTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Jenis</th> <!-- ← KOLOM BARU -->
                        <th class="text-end">Upah/Jam</th>
                        <th class="text-end">Upah/Hari (Est. 8 Jam)</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenagaKerja as $tenaga)
                    <tr>
                        <td><strong>{{ $tenaga->kode_tenaga }}</strong></td>
                        <td>{{ $tenaga->nama_tenaga }}</td>
                        <td>{{ $tenaga->jabatan }}</td>
                        
                        <!-- ========== KOLOM JENIS BARU ========== -->
                        <td>
                            @if(isset($tenaga->jenis_tenaga))
                                @if($tenaga->jenis_tenaga == 'langsung')
                                    <span class="badge bg-primary">Langsung</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Langsung</span>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark">Belum diset</span>
                            @endif
                        </td>
                        <!-- ==================================== -->
                        
                        <td class="text-end">Rp {{ number_format($tenaga->upah_per_jam, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <span class="text-muted">
                                Rp {{ number_format($tenaga->upah_per_jam * 8, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @if($tenaga->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('tenaga-kerja.edit', $tenaga->id_tenaga) }}" 
                                   class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmDelete({{ $tenaga->id_tenaga }})" title="Hapus">
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
        $('#tenagaKerjaTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25
        });
    });
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Tenaga Kerja?',
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
                form.action = '/tenaga-kerja/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection