@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Tambah Produk</h4>
            <p class="text-muted mb-0">Input data produk dan BOM bahan baku produksi.</p>
        </div>
        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<form action="{{ route('produk.store') }}" method="POST">
    @csrf

    <div class="card mb-4">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0"><i class="fas fa-box text-primary me-2"></i>Informasi Produk</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_produk" value="{{ $kode_produk }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" name="nama_produk" value="{{ old('nama_produk') }}" required>
                        @error('nama_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tipe Produk <span class="text-danger">*</span></label>
                        <select class="form-select @error('tipe_produk') is-invalid @enderror" name="tipe_produk" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="kulit" {{ old('tipe_produk') == 'kulit' ? 'selected' : '' }}>Kulit (WIP)</option>
                            <option value="isi" {{ old('tipe_produk') == 'isi' ? 'selected' : '' }}>Isi</option>
                            <option value="jadi" {{ old('tipe_produk') == 'jadi' ? 'selected' : '' }}>Barang Jadi</option>
                        </select>
                        @error('tipe_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Satuan Produk <span class="text-danger">*</span></label>
                        <select class="form-select @error('satuan_produk') is-invalid @enderror" name="satuan_produk" required>
                            <option value="">-- Pilih Satuan --</option>
                            <option value="Pcs" {{ old('satuan_produk') == 'Pcs' ? 'selected' : '' }}>Pieces (Pcs)</option>
                            <option value="Kg" {{ old('satuan_produk') == 'Kg' ? 'selected' : '' }}>Kilogram (Kg)</option>
                            <option value="Gram" {{ old('satuan_produk') == 'Gram' ? 'selected' : '' }}>Gram</option>
                            <option value="Liter" {{ old('satuan_produk') == 'Liter' ? 'selected' : '' }}>Liter</option>
                            <option value="Ml" {{ old('satuan_produk') == 'Ml' ? 'selected' : '' }}>Mililiter (Ml)</option>
                        </select>
                        @error('satuan_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-0">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" rows="2">{{ old('deskripsi') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-boxes text-success me-2"></i>BOM Bahan Baku</h6>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="tambahBomBahan()">
                    <i class="fas fa-plus me-1"></i>Tambah Bahan
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-light border mb-3">
                <i class="fas fa-info-circle text-info me-2"></i>
                Mesin tidak dicatat di BOM. Biaya operasional mesin dicatat di transaksi BOP melalui Gas, Listrik, atau Air dengan keterangan mesin yang digunakan.
            </div>
            <div id="bomBahanContainer"></div>
            <div id="bomBahanEmpty" class="text-center py-3 text-muted">
                <i class="fas fa-boxes fa-2x mb-2 d-block text-muted opacity-50"></i>
                Klik "Tambah Bahan" untuk menambahkan bahan baku ke BOM.
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Simpan Produk
        </button>
        <a href="{{ route('produk.index') }}" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i>Batal
        </a>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const daftarBahan = {!! $bahanBakuJson !!};
    let bomBahanCount = 0;

    function tambahBomBahan() {
        const container = document.getElementById('bomBahanContainer');
        document.getElementById('bomBahanEmpty').style.display = 'none';

        const options = daftarBahan.map(b =>
            `<option value="${b.id}">${b.kode} - ${b.nama} (${b.satuan})</option>`
        ).join('');

        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-end bom-bahan-row';
        row.innerHTML = `
            <div class="col-md-5">
                <label class="form-label small mb-1">Bahan</label>
                <select class="form-select form-select-sm" name="bom_bahan_id[]">
                    <option value="">-- Pilih Bahan --</option>
                    ${options}
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small mb-1">Keterangan</label>
                <input type="text" class="form-control form-control-sm" name="bom_bahan_keterangan[]" placeholder="Opsional">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.bom-bahan-row').remove(); cekEmptyBahan()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        bomBahanCount++;
    }

    function cekEmptyBahan() {
        const rows = document.querySelectorAll('.bom-bahan-row');
        document.getElementById('bomBahanEmpty').style.display = rows.length === 0 ? 'block' : 'none';
    }
</script>
@endsection
