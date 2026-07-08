@extends('layouts.app')

@section('title', 'Permintaan Bahan Baku')
@section('page-title', 'Permintaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Permintaan Bahan Baku</h4>
            <p class="text-muted mb-0">Kelola permintaan bahan baku dari produksi</p>
        </div>
        <a href="{{ route('permintaan-bahan-baku.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat Permintaan Baru
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
            <table id="permintaanBahanTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Nomor Permintaan</th>
                        <th>Tanggal</th>
                        <th>Bahan Baku</th>
                        <th class="text-end">Jumlah Permintaan</th>
                        <th class="text-center">Progress Penerimaan</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permintaanBahan as $permintaan)
                    <tr>
                        <!-- NOMOR PERMINTAAN -->
                        <td>
                            <strong>{{ $permintaan->nomor_permintaan }}</strong>
                        </td>
                        
                        <!-- TANGGAL -->
                        <td>{{ $permintaan->tanggal_permintaan->format('d/m/Y') }}</td>
                        
                        <!-- BAHAN BAKU -->
                        <td>
                            @if($permintaan->details->count() > 0)
                                <strong>{{ $permintaan->details->first()->bahanBaku->nama_bahan }}</strong>
                                @if($permintaan->details->count() > 1)
                                    <br><small class="text-muted">+ {{ $permintaan->details->count() - 1 }} bahan lainnya</small>
                                @endif
                            @else
                                <span class="text-muted">Tidak ada bahan</span>
                            @endif
                        </td>
                        
                        <!-- JUMLAH ITEM -->
                        <td class="text-end">
                            {{ $permintaan->details->count() }} Item
                        </td>
                        
                        <!-- PROGRESS PENERIMAAN -->
                        <td class="text-center">
                            @php
                                $statusPenerimaan = $permintaan->status_penerimaan;
                                $totalItem = $permintaan->details->count();
                                $completedItem = $permintaan->details->where('status_penerimaan', 'completed')->count();
                            @endphp
                            
                            <div class="mb-1">
                                <small>
                                    <strong class="text-primary">{{ $completedItem }}</strong> / {{ $totalItem }} Item Selesai
                                </small>
                            </div>
                            
                            <div class="progress mb-1" style="height: 10px;">
                                <div class="progress-bar @if($statusPenerimaan === 'completed') bg-success @elseif($statusPenerimaan === 'partial') bg-warning @else bg-secondary @endif" 
                                     role="progressbar" 
                                     style="width: {{ $totalItem > 0 ? ($completedItem / $totalItem * 100) : 0 }}%">
                                </div>
                            </div>
                        </td>
                        
                        <!-- KETERANGAN -->
                        <td>
                            @if($permintaan->keterangan)
                                {{ Str::limit($permintaan->keterangan, 50) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        
                        <!-- STATUS -->
                        <td>
                            @php $statusPenerimaan = $permintaan->status_penerimaan; @endphp
                            @if($statusPenerimaan === 'completed')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-double"></i> Selesai
                                </span>
                            @elseif($statusPenerimaan === 'partial')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-hourglass-half"></i> Sebagian Diterima
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-box-open"></i> Belum Diterima
                                </span>
                            @endif
                        </td>
                        
                        <!-- AKSI -->
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Tombol Detail - SELALU ADA -->
                                <a href="{{ route('permintaan-bahan-baku.show', $permintaan->id_permintaan_bahan) }}" 
                                   class="btn btn-info" 
                                   title="Detail"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($permintaan->status_penerimaan !== 'completed')
                                    <!-- Tombol Terima Bahan -->
                                    <a href="{{ route('penerimaan-bahan-baku.create') }}?permintaan={{ $permintaan->nomor_permintaan }}" 
                                       class="btn btn-primary" 
                                       title="Terima Bahan"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-box"></i>
                                        @if($permintaan->status_penerimaan === 'partial')
                                            Terima Lagi
                                        @else
                                            Terima
                                        @endif
                                    </a>
                                    
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('permintaan-bahan-baku.edit', $permintaan->id_permintaan_bahan) }}" 
                                       class="btn btn-warning" 
                                       title="Edit"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('permintaan-bahan-baku.destroy', $permintaan->id_permintaan_bahan) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus permintaan {{ $permintaan->nomor_permintaan }}? Data tidak dapat dikembalikan!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger" 
                                                title="Hapus"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-success p-2">
                                        <i class="fas fa-check-double"></i> Selesai
                                    </span>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable dengan konfigurasi lengkap
        $('#permintaanBahanTable').DataTable({
            order: [[0, 'desc']], // Sort by Nomor Permintaan descending
            pageLength: 25,
            language: {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            columnDefs: [
                { orderable: false, targets: [5, 7] }, // Keterangan dan Aksi tidak bisa di-sort
                { className: "text-center", targets: [4, 6, 7] } // Center align untuk kolom tertentu
            ]
        });
        
        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Log untuk debugging
        console.log('DataTable initialized successfully');
        console.log('Total rows:', $('#permintaanBahanTable tbody tr').length);
    });
</script>
@endsection