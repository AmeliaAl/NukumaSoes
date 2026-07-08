@extends('layouts.app')

@section('title', 'Edit Produk - ' . $produk->nama_produk)
@section('page-title', 'Edit Produk')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Edit Produk</h4>
            <p class="text-muted mb-0">{{ $produk->kode_produk }} - {{ $produk->nama_produk }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('produk.show', $produk->id_produk) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>Lihat Detail
            </a>
            <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<form action="{{ route('produk.update', $produk->id_produk) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0"><i class="fas fa-box text-primary me-2"></i>Informasi Produk</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" class="form-control" value="{{ $produk->kode_produk }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
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
                            <option value="kulit" {{ old('tipe_produk', $produk->tipe_produk) == 'kulit' ? 'selected' : '' }}>Kulit (WIP)</option>
                            <option value="isi" {{ old('tipe_produk', $produk->tipe_produk) == 'isi' ? 'selected' : '' }}>Isi</option>
                            <option value="jadi" {{ old('tipe_produk', $produk->tipe_produk) == 'jadi' ? 'selected' : '' }}>Barang Jadi</option>
                        </select>
                        @error('tipe_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Satuan Produk <span class="text-danger">*</span></label>
                        <select class="form-select @error('satuan_produk') is-invalid @enderror" name="satuan_produk" required>
                            @foreach(['Pcs' => 'Pieces (Pcs)', 'Kg' => 'Kilogram (Kg)', 'Gram' => 'Gram', 'Liter' => 'Liter', 'Ml' => 'Mililiter (Ml)'] as $value => $label)
                                <option value="{{ $value }}" {{ old('satuan_produk', $produk->satuan_produk) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('satuan_produk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="aktif" {{ old('status', $produk->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $produk->status) == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-0">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" rows="2">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
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
                Data BOM bahan akan diganti dengan data baru saat disimpan.
            </div>
            <div id="bomBahanContainer">
                @foreach($produk->bomBahan as $bom)
                <div class="row g-2 mb-2 align-items-end bom-bahan-row">
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Bahan</label>
                        <select class="form-select form-select-sm" name="bom_bahan_id[]">
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($bomOptions as $opt)
                                @php
                                    $isSelected = ($bom->id_bahan && $opt->id == 'bahan_' . $bom->id_bahan)
                                        || ($bom->id_produk_wip && $opt->id == 'wip_' . $bom->id_produk_wip);
                                @endphp
                                <option value="{{ $opt->id }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $opt->kode }} - {{ $opt->nama }} ({{ $opt->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Keterangan</label>
                        <input type="text" class="form-control form-control-sm" name="bom_bahan_keterangan[]" value="{{ $bom->keterangan }}" placeholder="Opsional">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.bom-bahan-row').remove(); cekEmptyBahan()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="bomBahanEmpty" class="text-center py-3 text-muted" style="{{ $produk->bomBahan->count() > 0 ? 'display:none' : '' }}">
                <i class="fas fa-boxes fa-2x mb-2 d-block text-muted opacity-50"></i>
                Klik "Tambah Bahan" untuk menambahkan bahan baku ke BOM.
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Update Produk
        </button>
        <a href="{{ route('produk.show', $produk->id_produk) }}" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i>Batal
        </a>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const daftarBahan = {!! $bahanBakuJson !!};

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
    }

    function cekEmptyBahan() {
        const rows = document.querySelectorAll('.bom-bahan-row');
        document.getElementById('bomBahanEmpty').style.display = rows.length === 0 ? 'block' : 'none';
    }
</script>
@endsection
