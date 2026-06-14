@extends('layouts.app')

@section('title', 'Terima Order Produksi Baru')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('penerimaan-order-produksi.index') }}" class="btn btn-sm btn-light" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-inbox me-2" style="color: #8e44ad;"></i>Terima Order Produksi Baru
            </h4>
            <p class="text-muted mb-0 small">Input kode order dari bagian penjualan/manajemen untuk memulai Job Order produksi</p>
        </div>
    </div>

    <form action="{{ route('penerimaan-order-produksi.store') }}" method="POST">
        @csrf

        <div class="row g-4">

            {{-- KOLOM KIRI: Info dari bagian lain --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #8e44ad, #9b59b6);">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-barcode text-white me-2 fs-5"></i>
                            <h6 class="text-white fw-bold mb-0">Data Order dari Bagian Lain</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert border-0 mb-4" style="background: #f3e5f5; border-radius: 10px;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-lightbulb" style="color: #8e44ad;"></i>
                                <small style="color: #6c3483;">
                                    Masukkan kode order yang diterima dari bagian Penjualan/Manajemen beserta detailnya.
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Kode Order Eksternal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #f3e5f5; border-color: #e0c3f0;">
                                    <i class="fas fa-barcode" style="color: #8e44ad;"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('kode_order_eksternal') is-invalid @enderror" 
                                       name="kode_order_eksternal"
                                       id="kode_order_eksternal"
                                       value="{{ old('kode_order_eksternal') }}"
                                       placeholder="Contoh: ORD-20260519-001"
                                       style="border-color: #e0c3f0;"
                                       required>
                                @error('kode_order_eksternal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Kode unik yang diberikan oleh bagian penjualan/manajemen</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Nama Pemesan / Sumber Order <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama_pemesan') is-invalid @enderror" 
                                   name="nama_pemesan"
                                   value="{{ old('nama_pemesan') }}"
                                   placeholder="Nama customer atau departemen yang meminta"
                                   style="border-color: #e0c3f0;">
                            @error('nama_pemesan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Tanggal Terima Order <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('tanggal_terima_order') is-invalid @enderror" 
                                   name="tanggal_terima_order"
                                   value="{{ old('tanggal_terima_order', date('Y-m-d')) }}"
                                   style="border-color: #e0c3f0;">
                            @error('tanggal_terima_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Keterangan Tambahan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      name="keterangan"
                                      rows="3"
                                      placeholder="Catatan tambahan dari bagian penjualan (opsional)"
                                      style="border-color: #e0c3f0;">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Detail Job Order --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #2980b9, #3498db);">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cogs text-white me-2 fs-5"></i>
                            <h6 class="text-white fw-bold mb-0">Detail Job Order Produksi</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Nomor Job Order (Auto) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control bg-light @error('nomor_job') is-invalid @enderror"
                                       name="nomor_job"
                                       value="{{ old('nomor_job', $nomor_job) }}"
                                       readonly
                                       style="border-color: #bee3f8; font-family: monospace; font-weight: 600; color: #2980b9;">
                                @error('nomor_job')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Dibuat otomatis oleh sistem</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Produk yang Diproduksi <span class="text-danger">*</span></label>
                                <select class="form-select @error('id_produk') is-invalid @enderror" 
                                        name="id_produk" required style="border-color: #bee3f8;">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id_produk }}" {{ old('id_produk') == $p->id_produk ? 'selected' : '' }}>
                                            {{ $p->nama_produk }} ({{ $p->tipe_produk ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Jumlah Produksi (Unit) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('jumlah_produksi') is-invalid @enderror"
                                       name="jumlah_produksi"
                                       value="{{ old('jumlah_produksi') }}"
                                       min="1" placeholder="0"
                                       style="border-color: #bee3f8;">
                                @error('jumlah_produksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Jumlah Batch <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('jumlah_batch') is-invalid @enderror"
                                       name="jumlah_batch"
                                       value="{{ old('jumlah_batch', 1) }}"
                                       min="1" placeholder="1"
                                       style="border-color: #bee3f8;">
                                @error('jumlah_batch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Tanggal Mulai Produksi <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                       name="tanggal_mulai"
                                       value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                                       style="border-color: #bee3f8;">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Jenis Produksi <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis_produksi') is-invalid @enderror" 
                                        name="jenis_produksi" style="border-color: #bee3f8;">
                                    <option value="maklun" {{ old('jenis_produksi') == 'maklun' ? 'selected' : '' }}>Maklun (Merk Lain)</option>
                                    <option value="brand_sendiri" {{ old('jenis_produksi') == 'brand_sendiri' ? 'selected' : '' }}>Brand Sendiri</option>
                                </select>
                                @error('jenis_produksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Tujuan Produksi <span class="text-danger">*</span></label>
                                <select class="form-select @error('tujuan_produksi') is-invalid @enderror" 
                                        name="tujuan_produksi" style="border-color: #bee3f8;">
                                    <option value="pesanan" {{ old('tujuan_produksi', 'pesanan') == 'pesanan' ? 'selected' : '' }}>Pesanan Customer</option>
                                    <option value="stok_wip" {{ old('tujuan_produksi') == 'stok_wip' ? 'selected' : '' }}>Stok WIP</option>
                                    <option value="stok_barang_jadi" {{ old('tujuan_produksi') == 'stok_barang_jadi' ? 'selected' : '' }}>Stok Barang Jadi</option>
                                </select>
                                @error('tujuan_produksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Tahap Produksi Awal <span class="text-danger">*</span></label>
                                <select class="form-select @error('tahap_produksi') is-invalid @enderror" 
                                        name="tahap_produksi" style="border-color: #bee3f8;">
                                    <option value="persiapan" {{ old('tahap_produksi', 'persiapan') == 'persiapan' ? 'selected' : '' }}>Persiapan</option>
                                    <option value="produksi" {{ old('tahap_produksi') == 'produksi' ? 'selected' : '' }}>Produksi</option>
                                    <option value="filling" {{ old('tahap_produksi') == 'filling' ? 'selected' : '' }}>Filling</option>
                                </select>
                                @error('tahap_produksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Tombol Simpan --}}
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('penerimaan-order-produksi.index') }}" class="btn btn-light px-4" style="border-radius: 10px;">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-5 fw-semibold" 
                            style="background: linear-gradient(135deg, #8e44ad, #9b59b6); border: none; border-radius: 10px;">
                        <i class="fas fa-check me-2"></i>Terima & Buat Job Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
