@extends('layouts.app')

@section('title', 'Tambah Tenaga Kerja')
@section('page-title', 'Tambah Tenaga Kerja')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Tambah Tenaga Kerja</h4>
            <p class="text-muted mb-0">Input data tenaga kerja baru</p>
        </div>
        <a href="{{ route('tenaga-kerja.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('tenaga-kerja.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" 
                            class="form-control" 
                            name="kode_tenaga"
                            value="{{ $kode_tenaga }}"
                            readonly>
                        <small class="text-muted">Kode otomatis</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama_tenaga') is-invalid @enderror" 
                               name="nama_tenaga" 
                               value="{{ old('nama_tenaga') }}"
                               placeholder="Contoh: Budi Santoso"
                               required>
                        @error('nama_tenaga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('jabatan') is-invalid @enderror" 
                               name="jabatan" 
                               value="{{ old('jabatan') }}"
                               placeholder="Contoh: Operator Mesin"
                               required>
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Bagian</label>
                        <input type="text" 
                               class="form-control @error('bagian') is-invalid @enderror" 
                               name="bagian" 
                               value="{{ old('bagian') }}"
                               placeholder="Contoh: Produksi Kulit">
                        @error('bagian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- ========== FIELD BARU: JENIS TENAGA KERJA ========== -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jenis Tenaga Kerja <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_tenaga') is-invalid @enderror" 
                                name="jenis_tenaga" 
                                id="jenisTenaga"
                                required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="langsung" {{ old('jenis_tenaga', 'langsung') == 'langsung' ? 'selected' : '' }}>
                                Tenaga Kerja Langsung
                            </option>
                            <option value="tidak_langsung" {{ old('jenis_tenaga') == 'tidak_langsung' ? 'selected' : '' }}>
                                Tenaga Kerja Tidak Langsung
                            </option>
                        </select>
                        <small class="text-muted">
                            <span id="jenisKeterangan">
                                <strong>Langsung:</strong> Terlibat langsung dalam produksi (Operator, Perakitan)<br>
                                <strong>Tidak Langsung:</strong> Support/overhead (Supervisor, QC, Maintenance)
                            </span>
                        </small>
                        @error('jenis_tenaga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- ================================================== -->
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Upah Per Jam <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('upah_per_jam') is-invalid @enderror" 
                                   name="upah_per_jam" 
                                   value="{{ old('upah_per_jam', '6000') }}"
                                   min="0"
                                   step="500"
                                   placeholder="6000"
                                   required>
                        </div>
                        <small class="text-muted">Estimasi upah/hari (8 jam): <span id="estimasiUpah">Rp 48.000</span></small>
                        @error('upah_per_jam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                name="status" required>
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong>
                <ul class="mb-0 mt-2">
                    <li>Upah per jam digunakan untuk menghitung biaya harian secara real-time berdasarkan kehadiran harian.</li>
                    <li><strong>Tenaga Kerja Langsung:</strong> Biaya masuk ke "Biaya Tenaga Kerja Langsung"</li>
                    <li><strong>Tenaga Kerja Tidak Langsung:</strong> Biaya masuk ke "Biaya Overhead Pabrik"</li>
                </ul>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
                <a href="{{ route('tenaga-kerja.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hitung estimasi upah harian
    document.querySelector('input[name="upah_per_jam"]').addEventListener('input', function() {
        const upahPerJam = parseFloat(this.value) || 0;
        const estimasiHarian = upahPerJam * 8;
        document.getElementById('estimasiUpah').textContent = 
            'Rp ' + estimasiHarian.toLocaleString('id-ID');
    });

    // Update keterangan jenis tenaga kerja
    document.getElementById('jenisTenaga').addEventListener('change', function() {
        const keterangan = document.getElementById('jenisKeterangan');
        
        if (this.value === 'langsung') {
            keterangan.innerHTML = '<strong class="text-primary">Tenaga Kerja Langsung:</strong> Terlibat langsung dalam proses produksi<br><small>Contoh: Operator Mesin, Operator Produksi, Perakitan, dll</small>';
        } else if (this.value === 'tidak_langsung') {
            keterangan.innerHTML = '<strong class="text-secondary">Tenaga Kerja Tidak Langsung:</strong> Support/overhead pabrik<br><small>Contoh: Supervisor, Quality Control, Maintenance, Cleaning Service, dll</small>';
        } else {
            keterangan.innerHTML = '<strong>Langsung:</strong> Terlibat langsung dalam produksi (Operator, Perakitan)<br><strong>Tidak Langsung:</strong> Support/overhead (Supervisor, QC, Maintenance)';
        }
    });
</script>
@endsection