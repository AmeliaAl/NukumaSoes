@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')
@section('page-title', 'Tambah Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Tambah Bahan Baku</h4>
            <p class="text-muted mb-0">Input data bahan baku baru</p>
        </div>
        <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('bahan-baku.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kode Bahan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('kode_bahan') is-invalid @enderror" 
                               name="kode_bahan" 
                               value="{{ old('kode_bahan', $kode_bahan) }}"
                               readonly>
                        <small class="text-muted">Kode otomatis</small>
                        @error('kode_bahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama_bahan') is-invalid @enderror" 
                               name="nama_bahan" 
                               value="{{ old('nama_bahan') }}"
                               placeholder="Contoh: Tepung Terigu"
                               required>
                        @error('nama_bahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Satuan Pakai (Terkecil) <span class="text-danger">*</span></label>
                        <select class="form-select @error('satuan') is-invalid @enderror" 
                                name="satuan" id="satuanPakai" required>
                            <option value="">-- Pilih Satuan --</option>
                            <option value="Kg" {{ old('satuan') == 'Kg' ? 'selected' : '' }}>Kilogram (Kg)</option>
                            <option value="Gram" {{ old('satuan') == 'Gram' ? 'selected' : '' }}>Gram</option>
                            <option value="Liter" {{ old('satuan') == 'Liter' ? 'selected' : '' }}>Liter</option>
                            <option value="Ml" {{ old('satuan') == 'Ml' ? 'selected' : '' }}>Mililiter (Ml)</option>
                            <option value="Pcs" {{ old('satuan') == 'Pcs' ? 'selected' : '' }}>Pieces (Pcs)</option>
                            <option value="Butir" {{ old('satuan') == 'Butir' ? 'selected' : '' }}>Butir</option>
                            <option value="Pack" {{ old('satuan') == 'Pack' ? 'selected' : '' }}>Pack</option>
                        </select>
                        @error('satuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('stok_minimum') is-invalid @enderror" 
                               name="stok_minimum" 
                               value="{{ old('stok_minimum') }}"
                               min="0"
                               step="0.01"
                               placeholder="Contoh: 10"
                               required>
                        <small class="text-muted">Batas stok untuk alert</small>
                        @error('stok_minimum')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Satuan Beli (Kemasan)</label>
                        <input type="text" 
                               class="form-control @error('satuan_beli') is-invalid @enderror" 
                               name="satuan_beli" 
                               value="{{ old('satuan_beli') }}"
                               placeholder="Contoh: Bungkus, Galon, Dus">
                        <small class="text-muted">Kosongkan jika sama dengan satuan pakai</small>
                        @error('satuan_beli')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Isi per Kemasan</label>
                        <input type="number" 
                               class="form-control @error('isi_per_kemasan') is-invalid @enderror" 
                               name="isi_per_kemasan" 
                               value="{{ old('isi_per_kemasan', 1) }}"
                               min="0.01"
                               step="0.01">
                        <small class="text-muted">Berapa <span id="labelSatuanUtama">satuan</span> dalam 1 kemasan beli?</small>
                        @error('isi_per_kemasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jenis Bahan <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_bahan') is-invalid @enderror" 
                                name="jenis_bahan" required>
                            <option value="langsung" {{ old('jenis_bahan') == 'langsung' ? 'selected' : '' }}>Bahan Baku Langsung</option>
                            <option value="tidak_langsung" {{ old('jenis_bahan') == 'tidak_langsung' ? 'selected' : '' }}>Bahan Baku Tidak Langsung / Kemasan (BOP)</option>
                        </select>
                        @error('jenis_bahan')
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
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="3"
                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong> Stok saat ini akan otomatis 0. Gunakan menu Penerimaan Bahan Baku untuk menambah stok.
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
                <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('satuanPakai').addEventListener('change', function() {
        let val = this.value || 'satuan';
        document.getElementById('labelSatuanUtama').textContent = val;
    });
    // Trigger on load
    if (document.getElementById('satuanPakai').value) {
        document.getElementById('labelSatuanUtama').textContent = document.getElementById('satuanPakai').value;
    }
</script>
@endsection