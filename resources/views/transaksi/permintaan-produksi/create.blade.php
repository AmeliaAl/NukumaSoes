@extends('layouts.app')

@section('title', 'Buat Job Order')
@section('page-title', 'Buat Job Order Baru')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Buat Job Order Produksi</h4>
            <p class="text-muted mb-0">Input pesanan produksi baru dengan metode Job Order Costing</p>
        </div>
        <a href="{{ route('permintaan-produksi.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('permintaan-produksi.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nomor Job Order <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nomor_job') is-invalid @enderror" 
                               name="nomor_job"
                               value="{{ old('nomor_job', $nomor_job) }}"
                               readonly>
                        <small class="text-muted">Nomor otomatis</small>
                        @error('nomor_job')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                               name="tanggal_mulai" 
                               value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                               required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Produk <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_produk') is-invalid @enderror" 
                                name="id_produk" 
                                id="produkSelect"
                                required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produk as $item)
                                <option value="{{ $item->id_produk }}" 
                                        data-satuan="{{ $item->satuan_produk }}"
                                        {{ old('id_produk') == $item->id_produk ? 'selected' : '' }}>
                                    {{ $item->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Produksi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('jumlah_produksi') is-invalid @enderror" 
                                   name="jumlah_produksi" 
                                   value="{{ old('jumlah_produksi') }}"
                                   min="1"
                                   step="1"
                                   placeholder="Contoh: 100"
                                   required>
                            <span class="input-group-text" id="satuanProduk">Unit</span>
                        </div>
                        @error('jumlah_produksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Batch <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('jumlah_batch') is-invalid @enderror" 
                               name="jumlah_batch" 
                               value="{{ old('jumlah_batch', 1) }}"
                               min="1"
                               step="1"
                               required>
                        <small class="text-muted">Jumlah batch rencana untuk menyelesaikan satu Job Order.</small>
                        @error('jumlah_batch')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kapasitas Batch per Hari <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control @error('kapasitas_batch_per_hari') is-invalid @enderror"
                               name="kapasitas_batch_per_hari"
                               value="{{ old('kapasitas_batch_per_hari', 2) }}"
                               min="1"
                               step="1"
                               required>
                        <small class="text-muted">Dipakai untuk membuat jadwal awal batch otomatis. Contoh 4 batch dan kapasitas 2/hari = selesai dalam 2 hari.</small>
                        @error('kapasitas_batch_per_hari')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jenis Produksi <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_produksi') is-invalid @enderror" 
                                name="jenis_produksi" id="jenisProduksi" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="maklun" {{ old('jenis_produksi') == 'maklun' ? 'selected' : '' }}>Maklun (Pihak Ketiga)</option>
                            <option value="brand_sendiri" {{ old('jenis_produksi') == 'brand_sendiri' ? 'selected' : '' }}>Brand Sendiri</option>
                        </select>
                        @error('jenis_produksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tujuan Produksi <span class="text-danger">*</span></label>
                        <select class="form-select @error('tujuan_produksi') is-invalid @enderror" 
                                name="tujuan_produksi" required>
                            <option value="">-- Pilih Tujuan --</option>
                            <option value="pesanan" {{ old('tujuan_produksi') == 'pesanan' ? 'selected' : '' }}>Berdasarkan Pesanan</option>
                            <option value="stok_wip" {{ old('tujuan_produksi') == 'stok_wip' ? 'selected' : '' }}>Stok Setengah Jadi (WIP)</option>
                            <option value="stok_barang_jadi" {{ old('tujuan_produksi') == 'stok_barang_jadi' ? 'selected' : '' }}>Stok Barang Jadi</option>
                        </select>
                        @error('tujuan_produksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tahap Produksi <span class="text-danger">*</span></label>
                        <select class="form-select @error('tahap_produksi') is-invalid @enderror" 
                                name="tahap_produksi" required>
                            <option value="persiapan" {{ old('tahap_produksi') == 'persiapan' ? 'selected' : '' }}>Persiapan</option>
                            <option value="produksi" {{ old('tahap_produksi') == 'produksi' ? 'selected' : '' }}>Produksi / Kulit</option>
                            <option value="filling" {{ old('tahap_produksi') == 'filling' ? 'selected' : '' }}>Filling / Isi</option>
                            <option value="selesai" {{ old('tahap_produksi') == 'selesai' ? 'selected' : '' }}>Selesai / Pengemasan</option>
                        </select>
                        @error('tahap_produksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6" id="maklunCustomerContainer" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Nama Customer Maklun</label>
                        <input type="text" 
                               class="form-control @error('nama_customer_maklun') is-invalid @enderror" 
                               name="nama_customer_maklun" 
                               value="{{ old('nama_customer_maklun') }}"
                               placeholder="Merk Maklun">
                        @error('nama_customer_maklun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Customer / Pemesan</label>
                        <input type="text" 
                               class="form-control @error('customer') is-invalid @enderror" 
                               name="customer" 
                               value="{{ old('customer') }}"
                               placeholder="Contoh: Toko Sumber Rejeki">
                        <small class="text-muted">Opsional</small>
                        @error('customer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                name="status" required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="proses" {{ old('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                        </select>
                        <small class="text-muted">Pending = Belum dimulai, Proses = Sedang produksi</small>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="3"
                          placeholder="Keterangan tambahan tentang job order ini (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi Job Order Costing:</strong>
                <ul class="mb-0 mt-2">
                    <li>Setelah job order dibuat, input <strong>Pemakaian Bahan Baku</strong> (metode FIFO)</li>
                    <li>Input <strong>Biaya Tenaga Kerja</strong> (jam kerja × upah)</li>
                    <li>Input <strong>Biaya Overhead Pabrik</strong> (listrik, air, dll)</li>
                    <li>Sistem akan otomatis menghitung <strong>Total Biaya Produksi</strong></li>
                    <li>Setelah selesai, klik "Selesaikan Job Order" untuk finalisasi biaya</li>
                </ul>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Job Order
                </button>
                <a href="{{ route('permintaan-produksi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update satuan ketika produk dipilih
    document.getElementById('produkSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const satuan = selectedOption.getAttribute('data-satuan') || 'Unit';
        document.getElementById('satuanProduk').textContent = satuan;
    });
    
    // Trigger on page load if old value exists
    if (document.getElementById('produkSelect').value) {
        document.getElementById('produkSelect').dispatchEvent(new Event('change'));
    }

    // Tampilkan field Maklun jika jenis_produksi = maklun
    document.getElementById('jenisProduksi').addEventListener('change', function() {
        if (this.value === 'maklun') {
            document.getElementById('maklunCustomerContainer').style.display = 'block';
        } else {
            document.getElementById('maklunCustomerContainer').style.display = 'none';
        }
    });

    if (document.getElementById('jenisProduksi').value === 'maklun') {
        document.getElementById('maklunCustomerContainer').style.display = 'block';
    }
</script>
@endsection
