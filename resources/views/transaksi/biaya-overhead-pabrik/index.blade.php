@extends('layouts.app')

@section('title', 'Biaya Overhead Pabrik')
@section('page-title', 'Biaya Overhead Pabrik')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Biaya Overhead Pabrik</h4>
            <p class="text-muted mb-0">Input biaya overhead pabrik untuk setiap job order</p>
        </div>
        <a href="{{ route('biaya-overhead-pabrik.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Input Biaya Overhead
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
            <table id="biayaOverheadTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Job Order</th>
                        <th>Produk</th>
                        <th>Jenis Overhead</th>
                        <th>Satuan</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biayaOverhead as $overhead)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($overhead->tanggal)->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('permintaan-produksi.show', $overhead->id_permintaan_produksi) }}">
                                {{ $overhead->nomor_job }}
                            </a>
                        </td>
                        <td>{{ $overhead->nama_produk }}</td>
                        <td>
                            <span class="badge {{ $overhead->badge_class }}">{{ $overhead->jenis_overhead }}</span>
                        </td>
                        <td>
                            @if($overhead->satuan == 'per_bulan')
                                <span class="badge bg-warning text-dark">Per Bulan</span>
                            @elseif($overhead->satuan == 'per_minggu')
                                <span class="badge bg-info text-dark">Per Minggu</span>
                            @elseif($overhead->satuan == 'per_hari')
                                <span class="badge bg-secondary">Per Hari</span>
                            @elseif($overhead->satuan == 'per_batch')
                                <span class="badge bg-primary">Per Batch</span>
                            @elseif($overhead->satuan == 'per_jam')
                                <span class="badge bg-dark text-white">Per Jam</span>
                            @else
                                <span class="badge bg-success">Bahan Baku</span>
                            @endif
                        </td>
                        <td class="text-end">Rp {{ number_format($overhead->nominal, 0, ',', '.') }}</td>
                        <td class="text-end">
                            @if($overhead->satuan == 'bahan_baku')
                                {{ number_format($overhead->jumlah, 2, ',', '.') }}
                            @elseif($overhead->satuan == 'per_jam')
                                {{ number_format($overhead->jumlah, 1, ',', '.') }}
                            @else
                                {{ number_format($overhead->jumlah, 0, ',', '.') }}
                            @endif
                            {{ $overhead->satuan_label }}
                        </td>
                        <td class="text-end">
                            <strong class="text-primary">Rp {{ number_format($overhead->total_biaya, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-center">
                            @if(!$overhead->is_otomatis)
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('biaya-overhead-pabrik.edit', $overhead->id) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDelete({{ $overhead->id }})" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @else
                                @if($overhead->tipe == 'btktl')
                                    <span class="badge bg-secondary text-dark border" title="{{ $overhead->keterangan }}">
                                        <i class="fas fa-robot me-1 text-primary"></i>Otomatis (Absensi)
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-dark border" title="{{ $overhead->keterangan }}">
                                        <i class="fas fa-robot me-1 text-success"></i>Otomatis (Bahan)
                                    </span>
                                @endif
                            @endif
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
        $('#biayaOverheadTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Biaya Overhead?',
            text: "Data yang sudah dihapus tidak dapat dikembalikan dan akan mempengaruhi total biaya job order!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/biaya-overhead-pabrik/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection