@extends('layouts.app')

@section('title', 'Edit Kategori BOP')
@section('page-title', 'Edit Kategori BOP')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Edit Kategori BOP</h4>
            <p class="text-muted mb-0">Ubah data kategori BOP: {{ $kategori->nama_kategori }}</p>
        </div>
        <a href="{{ route('kategori-bop.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori-bop.update', $kategori->id_kategori_bop) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control @error('nama_kategori') is-invalid @enderror" 
                       name="nama_kategori" 
                       value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                       required>
                @error('nama_kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="4">{{ old('keterangan', $kategori->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('kategori-bop.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
