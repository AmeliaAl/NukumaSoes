<!-- resources/views/perolehan-aset/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Perolehan Aset</h2>
        <a href="{{ route('perolehan-aset.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Perolehan Aset
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <!-- Filter Section -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama aset..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="dari_tanggal" class="form-control" placeholder="Dari Tanggal" value="{{ request('dari_tanggal') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="sampai_tanggal" class="form-control" placeholder="Sampai Tanggal" value="{{ request('sampai_tanggal') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Faktur</th>
                            <th>Tanggal Faktur</th>
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Vendor</th>
                            <th>Qty</th>
                            <th>Total Perolehan</th>
                            <th>Tgl Pakai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perolehanAsets as $index => $item)
                        <tr>
                            <td>{{ $perolehanAsets->firstItem() + $index }}</td>
                            <td>{{ $item->no_faktur }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_faktur)->format('d/m/Y') }}</td>
                            <td>{{ $item->nama_aset }}</td>
                            <td>{{ $item->kategoriAset->nama_kategori }}</td>
                            <td>{{ $item->vendor->nama_vendor }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp {{ number_format($item->total_perolehan, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_pakai)->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('perolehan-aset.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('perolehan-aset.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $item->id }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">Tidak ada data perolehan aset</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Menampilkan {{ $perolehanAsets->firstItem() ?? 0 }} sampai {{ $perolehanAsets->lastItem() ?? 0 }} 
                    dari {{ $perolehanAsets->total() }} data
                </div>
                <div>
                    {{ $perolehanAsets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const form = document.getElementById('delete-form');
        form.action = `/perolehan-aset/${id}`;
        form.submit();
    }
}
</script>
@endsection