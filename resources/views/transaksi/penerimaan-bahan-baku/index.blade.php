@extends('layouts.app')

@section('title', 'Penerimaan Bahan Baku')
@section('page-title', 'Penerimaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Penerimaan Bahan Baku</h4>
            <p class="text-muted mb-0">Kelola penerimaan bahan baku dan update stok (FIFO)</p>
        </div>
        <a href="{{ route('penerimaan-bahan-baku.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Input Penerimaan Baru
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
            <table id="penerimaanBahanTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Nomor Penerimaan</th>
                        <th>Tanggal</th>
                        <th>Bahan Baku</th>
                        <th class="text-end">Jumlah Item</th>
                        <th class="text-end">Total Biaya</th>
                        <th>Dari Permintaan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penerimaanBahan as $penerimaan)
                    <tr>
                        <td><strong>{{ $penerimaan->nomor_penerimaan }}</strong></td>
                        <td>{{ $penerimaan->tanggal_penerimaan->format('d/m/Y') }}</td>
                        <!-- BAHAN BAKU -->
                        <td>
                            @if($penerimaan->details->count() > 0)
                                <strong>{{ $penerimaan->details->first()->bahanBaku->nama_bahan }}</strong>
                                @if($penerimaan->details->count() > 1)
                                    <br><small class="text-muted">+ {{ $penerimaan->details->count() - 1 }} bahan lainnya</small>
                                @endif
                            @else
                                <span class="text-muted">Tidak ada bahan</span>
                            @endif
                        </td>
                        
                        <!-- JUMLAH ITEM -->
                        <td class="text-end">
                            {{ $penerimaan->details->count() }} Item
                        </td>
                        <td class="text-end">
                            <strong class="text-success">Rp {{ number_format($penerimaan->total_biaya, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            @if($penerimaan->permintaanBahanBaku)
                                <a href="{{ route('permintaan-bahan-baku.show', $penerimaan->id_permintaan_bahan) }}">
                                    {{ $penerimaan->permintaanBahanBaku->nomor_permintaan }}
                                </a>
                            @else
                                <span class="text-muted">Langsung</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('penerimaan-bahan-baku.show', $penerimaan->id_penerimaan) }}" 
                                   class="btn btn-info" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('penerimaan-bahan-baku.edit', $penerimaan->id_penerimaan) }}" 
                                   class="btn btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmDelete({{ $penerimaan->id_penerimaan }})" 
                                        title="Hapus">
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
        $('#penerimaanBahanTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Penerimaan?',
            text: "Stok akan dikurangi dan data tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/penerimaan-bahan-baku/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection