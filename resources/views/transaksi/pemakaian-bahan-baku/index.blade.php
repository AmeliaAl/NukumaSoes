@extends('layouts.app')

@section('title', 'Pemakaian Bahan Baku')
@section('page-title', 'Pemakaian Bahan Baku (FIFO)')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Pemakaian Bahan Baku (FIFO Method)</h4>
            <p class="text-muted mb-0">Input pemakaian bahan baku untuk job order dengan metode FIFO</p>
        </div>
        <a href="{{ route('pemakaian-bahan-baku.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Input Pemakaian Bahan
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        
        @if(session('processed_items'))
            <div class="mt-3">
                <strong>Detail Pemakaian:</strong>
                <ul class="mb-0 mt-2">
                    @foreach(session('processed_items') as $item)
                        <li>
                            <strong>{{ $item['nama_bahan'] }}</strong>: 
                            {{ number_format($item['jumlah'], 2) }} {{ $item['satuan'] }} 
                            - Biaya: Rp {{ number_format($item['total_biaya'], 0, ',', '.') }}
                            @if(count($item['batches']) > 1)
                                <small class="text-muted">({{ count($item['batches']) }} batches FIFO)</small>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <hr>
                <strong>Total Biaya Keseluruhan: 
                    <span class="text-success">Rp {{ number_format(session('total_biaya'), 0, ',', '.') }}</span>
                </strong>
            </div>
        @endif
        
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
            <table id="pemakaianBahanTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Job Order</th>
                        <th>Produk</th>
                        <th>Bahan Baku</th>
                        <th class="text-end">Jumlah Pakai</th>
                        <th class="text-end">Harga/Satuan</th>
                        <th class="text-end">Total Biaya</th>
                        <th>Dari Batch</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemakaianBahan as $pakai)
                    <tr>
                        <td>{{ $pakai->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('permintaan-produksi.show', $pakai->id_permintaan_produksi) }}">
                                {{ $pakai->permintaanProduksi->nomor_job }}
                            </a>
                        </td>
                        <td>{{ $pakai->permintaanProduksi->produk->nama_produk }}</td>
                        <td>
                            @if($pakai->id_bahan && $pakai->bahanBaku)
                                <strong>{{ $pakai->bahanBaku->nama_bahan }}</strong>
                                <br>
                                <small class="text-muted">{{ $pakai->bahanBaku->kode_bahan }} (Bahan Baku)</small>
                            @elseif($pakai->id_produk_wip && $pakai->produkWip)
                                <strong>{{ $pakai->produkWip->nama_produk }}</strong>
                                <br>
                                <small class="text-muted">{{ $pakai->produkWip->kode_produk }} (WIP - {{ ucfirst($pakai->produkWip->tipe_produk) }})</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($pakai->id_bahan && $pakai->bahanBaku)
                                {{ number_format($pakai->jumlah_pakai, 2) }} {{ $pakai->bahanBaku->satuan }}
                            @elseif($pakai->id_produk_wip && $pakai->produkWip)
                                {{ number_format($pakai->jumlah_pakai, 2) }} {{ $pakai->produkWip->satuan_produk }}
                            @else
                                {{ number_format($pakai->jumlah_pakai, 2) }}
                            @endif
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($pakai->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="text-end">
                            <strong class="text-primary">Rp {{ number_format($pakai->total_biaya, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            @if($pakai->id_bahan && $pakai->stokBahanBaku)
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $pakai->stokBahanBaku->tanggal_masuk->format('d/m/Y') }}
                                </small>
                                <br>
                                <small class="badge bg-secondary">#{{ $pakai->id_stok }}</small>
                            @elseif($pakai->id_produk_wip && $pakai->stokProduk)
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $pakai->stokProduk->tanggal_masuk ? $pakai->stokProduk->tanggal_masuk->format('d/m/Y') : $pakai->stokProduk->created_at->format('d/m/Y') }}
                                </small>
                                <br>
                                <small class="badge bg-secondary">#{{ $pakai->id_stok_produk }}</small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-info" 
                                        onclick="showDetail({{ $pakai->id_pemakaian }})"
                                        title="Detail FIFO">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmDelete({{ $pakai->id_pemakaian }})" 
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

<!-- Modal Detail FIFO -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Detail Pemakaian FIFO
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content loaded via AJAX -->
            </div>
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
        $('#pemakaianBahanTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
    
    function showDetail(id) {
        // Show modal dengan loading
        $('#detailModal').modal('show');
        $('#modalContent').html(`
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                <p class="text-muted">Memuat detail pemakaian...</p>
            </div>
        `);
        
        // Load detail via AJAX
        $.ajax({
            url: '/pemakaian-bahan-baku/' + id + '/detail',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    let html = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Informasi Pemakaian</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Job Order:</strong></td>
                                        <td>${response.data.nomor_job}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Produk:</strong></td>
                                        <td>${response.data.nama_produk}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bahan Baku:</strong></td>
                                        <td>${response.data.nama_bahan}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal:</strong></td>
                                        <td>${response.data.tanggal}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Ringkasan Biaya</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Jumlah Pakai:</strong></td>
                                        <td>${response.data.jumlah_pakai} ${response.data.satuan}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Harga/Satuan:</strong></td>
                                        <td>Rp ${parseInt(response.data.harga_satuan).toLocaleString('id-ID')}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Biaya:</strong></td>
                                        <td><h5 class="text-success mb-0">Rp ${parseInt(response.data.total_biaya).toLocaleString('id-ID')}</h5></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">
                            <i class="fas fa-boxes me-2"></i>Detail Batch FIFO yang Digunakan
                        </h6>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Sistem menggunakan metode FIFO (First In First Out) untuk mengambil stok dari batch yang paling lama masuk terlebih dahulu.
                            </small>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Batch ID</th>
                                        <th>Tanggal Masuk</th>
                                        <th class="text-end">Jumlah Diambil</th>
                                        <th class="text-end">Harga/Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    // Tampilkan detail batch
                    html += `
                        <tr>
                            <td><span class="badge bg-secondary">#${response.data.id_stok}</span></td>
                            <td>${response.data.tanggal_batch}</td>
                            <td class="text-end">${response.data.jumlah_pakai} ${response.data.satuan}</td>
                            <td class="text-end">Rp ${parseInt(response.data.harga_satuan).toLocaleString('id-ID')}</td>
                            <td class="text-end"><strong>Rp ${parseInt(response.data.total_biaya).toLocaleString('id-ID')}</strong></td>
                        </tr>
                    `;
                    
                    html += `
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">TOTAL BIAYA:</th>
                                        <th class="text-end text-success">Rp ${parseInt(response.data.total_biaya).toLocaleString('id-ID')}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        ${response.data.keterangan ? `
                        <div class="mt-3">
                            <h6 class="text-muted">Keterangan:</h6>
                            <p class="mb-0">${response.data.keterangan}</p>
                        </div>
                        ` : ''}
                    `;
                    
                    $('#modalContent').html(html);
                } else {
                    $('#modalContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Gagal memuat detail: ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#modalContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Terjadi kesalahan saat memuat data. Silakan coba lagi.
                    </div>
                `);
            }
        });
    }
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Pemakaian Bahan?',
            text: "Stok akan dikembalikan dan biaya job order akan diupdate!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/pemakaian-bahan-baku/' + id;
                form.submit();
            }
        });
    }
</script>
@endsection