@extends('layouts.app')

@section('title', 'Data Stok Produk')
@section('page-title', 'Data Stok Produk (WIP & Barang Jadi)')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total Stok WIP</h6>
                        <h3 class="mb-0">{{ number_format($totalWip, 2, ',', '.') }}</h3>
                    </div>
                    <div class="icon-shape bg-white text-primary rounded-circle p-3">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Total Stok Jadi</h6>
                        <h3 class="mb-0">{{ number_format($totalJadi, 2, ',', '.') }}</h3>
                    </div>
                    <div class="icon-shape bg-white text-success rounded-circle p-3">
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Nilai WIP</h6>
                        <h3 class="mb-0">Rp {{ number_format($nilaiWip, 0, ',', '.') }}</h3>
                    </div>
                    <div class="icon-shape bg-white text-warning rounded-circle p-3">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Nilai Barang Jadi</h6>
                        <h3 class="mb-0">Rp {{ number_format($nilaiJadi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="icon-shape bg-white text-info rounded-circle p-3">
                        <i class="fas fa-coins fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white pb-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Stok Produk</h5>
            <div>
                <!-- You can add buttons here if needed, like Export to Excel -->
            </div>
        </div>
        
        <form action="{{ route('stok-produk.index') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <select name="tipe_stok" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Tipe Stok</option>
                    <option value="wip_kulit" {{ request('tipe_stok') == 'wip_kulit' ? 'selected' : '' }}>WIP - Kulit</option>
                    <option value="barang_jadi" {{ request('tipe_stok') == 'barang_jadi' ? 'selected' : '' }}>Barang Jadi</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="terjual" {{ request('status') == 'terjual' ? 'selected' : '' }}>Terjual / Dipakai</option>
                    <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                </select>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                @if(request()->has('tipe_stok') || request()->has('status'))
                    <a href="{{ route('stok-produk.index') }}" class="btn btn-outline-secondary">Reset Filter</a>
                @endif
            </div>
        </form>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Masuk</th>
                        <th>Job Order</th>
                        <th>Nama Produk</th>
                        <th>Tipe</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-end">HPP / Unit</th>
                        <th class="text-end">Total Nilai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokProduk as $stok)
                        <tr>
                            <td>{{ $stok->tanggal_masuk->format('d/m/Y') }}</td>
                            <td>
                                @if($stok->permintaanProduksi)
                                    <a href="{{ route('permintaan-produksi.show', $stok->id_permintaan_produksi) }}">
                                        {{ $stok->permintaanProduksi->nomor_job }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $stok->produk->nama_produk ?? '-' }}</td>
                            <td>{!! $stok->tipe_badge !!}</td>
                            <td class="text-end">
                                <strong>{{ number_format($stok->jumlah, 2, ',', '.') }} {{ $stok->satuan }}</strong>
                            </td>
                            <td class="text-end">Rp {{ number_format($stok->harga_pokok_per_unit, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <strong class="text-primary">Rp {{ number_format($stok->total_nilai, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($stok->status == 'tersedia')
                                    <span class="badge bg-success">Tersedia</span>
                                @elseif($stok->status == 'terjual')
                                    <span class="badge bg-info">Terjual/Dipakai</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($stok->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('stok-produk.show', $stok->id_stok_produk) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('stok-produk.destroy', $stok->id_stok_produk) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data stok ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Belum ada data stok produk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-3">
            {{ $stokProduk->links() }}
        </div>
    </div>
</div>
@endsection
