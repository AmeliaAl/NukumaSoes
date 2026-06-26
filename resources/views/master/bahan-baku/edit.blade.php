@extends('layouts.app')

@section('title', 'Edit Bahan Baku')
@section('page-title', 'Edit Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Edit Bahan Baku</h4>
            <p class="text-muted mb-0">Update data bahan baku</p>
        </div>
        <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('bahan-baku.update', $bahan->id_bahan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kode Bahan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $bahan->kode_bahan }}"
                               readonly>
                        <small class="text-muted">Kode tidak dapat diubah</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama_bahan') is-invalid @enderror" 
                               name="nama_bahan" 
                               value="{{ old('nama_bahan', $bahan->nama_bahan) }}"
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
                            <option value="Kg" {{ old('satuan', $bahan->satuan) == 'Kg' ? 'selected' : '' }}>Kilogram (Kg)</option>
                            <option value="Gram" {{ old('satuan', $bahan->satuan) == 'Gram' ? 'selected' : '' }}>Gram</option>
                            <option value="Liter" {{ old('satuan', $bahan->satuan) == 'Liter' ? 'selected' : '' }}>Liter</option>
                            <option value="Ml" {{ old('satuan', $bahan->satuan) == 'Ml' ? 'selected' : '' }}>Mililiter (Ml)</option>
                            <option value="Butir" {{ old('satuan', $bahan->satuan) == 'Butir' ? 'selected' : '' }}>Butir</option>
                            <option value="Pcs" {{ old('satuan', $bahan->satuan) == 'Pcs' ? 'selected' : '' }}>Pcs</option>
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
                               value="{{ old('stok_minimum', $bahan->stok_minimum) }}"
                               min="0"
                               step="0.01"
                               required>
                        @error('stok_minimum')
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
                            <option value="langsung" {{ old('jenis_bahan', $bahan->jenis_bahan) == 'langsung' ? 'selected' : '' }}>Bahan Baku Langsung</option>
                            <option value="tidak_langsung" {{ old('jenis_bahan', $bahan->jenis_bahan) == 'tidak_langsung' ? 'selected' : '' }}>Bahan Penolong / Tidak Langsung (BOP)</option>
                        </select>
                        <small class="text-muted">Contoh: toples, pouch, label, atau bahan penolong lain. Satuan tetap memakai satuan ukur seperti Pcs.</small>
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
                            <option value="aktif" {{ old('status', $bahan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $bahan->status) == 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Stok Saat Ini</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ number_format($bahan->stok_saat_ini, 2) }} {{ $bahan->satuan }}"
                               readonly>
                        <small class="text-muted">Stok tidak dapat diubah disini</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="3">{{ old('keterangan', $bahan->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update
                </button>
                <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
