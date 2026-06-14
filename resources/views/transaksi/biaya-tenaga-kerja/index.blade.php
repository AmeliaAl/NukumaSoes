@extends('layouts.app')

@section('title', 'Biaya Tenaga Kerja')
@section('page-title', 'Biaya Tenaga Kerja')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Biaya Tenaga Kerja (BTK)</h4>
            <p class="text-muted mb-0">Daftar biaya tenaga kerja teralokasi otomatis ke setiap job order</p>
        </div>
        <a href="{{ route('kehadiran.index') }}" class="btn btn-success shadow-sm rounded-pill px-4">
            <i class="fas fa-calendar-check me-2"></i>Kelola via Absensi Harian
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 bg-light">
    <div class="card-body py-3 d-flex align-items-center">
        <span class="badge bg-success-subtle text-success p-3 rounded-circle me-3">
            <i class="fas fa-info-circle fa-lg"></i>
        </span>
        <div>
            <h6 class="fw-bold mb-1 text-dark">Alokasi Terpusat Otomatis Aktif</h6>
            <p class="mb-0 text-muted" style="font-size: 13px;">
                Pencatatan Biaya Tenaga Kerja saat ini terintegrasi sepenuhnya dengan **Absensi Harian**. 
                Penambahan, perubahan, dan penghapusan data alokasi dilakukan secara otomatis oleh sistem saat Anda menginput kehadiran pekerja.
            </p>
        </div>
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
            <table id="biayaTenagaKerjaTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Job Order</th>
                        <th>Produk</th>
                        <th>Tenaga Kerja</th>
                        <th>Jabatan</th>
                        <th class="text-end">Jam Kerja</th>
                        <th class="text-end">Upah/Jam</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biayaTenagaKerja as $biaya)
                    <tr>
                        <td>{{ $biaya->tanggal_kerja->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('permintaan-produksi.show', $biaya->id_permintaan_produksi) }}">
                                {{ $biaya->permintaanProduksi->nomor_job }}
                            </a>
                        </td>
                        <td>{{ $biaya->permintaanProduksi->produk->nama_produk }}</td>
                        <td><strong>{{ $biaya->tenagaKerja->nama_tenaga }}</strong></td>
                        <td>{{ $biaya->tenagaKerja->jabatan }}</td>
                        <td class="text-end">{{ number_format($biaya->jam_kerja, 1) }} jam</td>
                        <td class="text-end">Rp {{ number_format($biaya->upah_per_jam, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <strong class="text-primary">Rp {{ number_format($biaya->total_biaya, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill shadow-sm" style="font-size: 12px;">
                                <i class="fas fa-lock me-1 text-warning"></i>Otomatis (Absensi)
                            </span>
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
        $('#biayaTenagaKerjaTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Biaya Tenaga Kerja?',
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
                form.action = '/biaya-tenaga-kerja/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection