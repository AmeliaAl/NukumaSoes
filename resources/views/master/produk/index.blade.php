@extends('layouts.app')

@section('title', 'Produk')
@section('page-title', 'Data Produk')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Data Produk</h4>
            <p class="text-muted mb-0">Kelola data produk hasil produksi</p>
        </div>
        <a href="{{ route('produk.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Produk
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="produkTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Tipe</th>
                        <th>Satuan</th>
                        <th class="text-center">BOM Bahan</th>
                        <th class="text-center">BOM Mesin</th>
                        <th class="text-center">Job Order</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produk as $item)
                    <tr>
                        <td><strong>{{ $item->kode_produk }}</strong></td>
                        <td>
                            <a href="{{ route('produk.show', $item->id_produk) }}" class="text-dark text-decoration-none fw-semibold">
                                {{ $item->nama_produk }}
                            </a>
                        </td>
                        <td>
                            @if($item->tipe_produk == 'kulit')
                                <span class="badge bg-secondary">Kulit (WIP)</span>
                            @elseif($item->tipe_produk == 'isi')
                                <span class="badge bg-info">Isi</span>
                            @else
                                <span class="badge bg-success">Barang Jadi</span>
                            @endif
                        </td>
                        <td>{{ $item->satuan_produk }}</td>
                        <td class="text-center">
                            @if($item->bomBahan->count() > 0)
                                <span class="badge bg-success">{{ $item->bomBahan->count() }} bahan</span>
                            @else
                                <span class="badge bg-light text-muted border">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->bomMesin->count() > 0)
                                <span class="badge bg-warning text-dark">{{ $item->bomMesin->count() }} mesin</span>
                            @else
                                <span class="badge bg-light text-muted border">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $item->permintaanProduksi->count() }}</span>
                        </td>
                        <td>
                            @if($item->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Non-aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('produk.show', $item->id_produk) }}" 
                                   class="btn btn-info" title="Detail BOM">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('produk.edit', $item->id_produk) }}" 
                                   class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmDelete({{ $item->id_produk }})" title="Hapus">
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
        $('#produkTable').DataTable({
            order: [[0, 'asc']]
        });
    });
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Produk?',
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
                form.action = '/produk/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection