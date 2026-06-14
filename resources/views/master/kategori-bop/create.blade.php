@extends('layouts.app')

@section('title', 'Tambah Kategori BOP')
@section('page-title', 'Tambah Kategori BOP')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Tambah Kategori BOP</h4>
            <p class="text-muted mb-0">Input data kategori BOP baru</p>
        </div>
        <a href="{{ route('kategori-bop.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori-bop.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control @error('nama_kategori') is-invalid @enderror" 
                       name="nama_kategori" 
                       value="{{ old('nama_kategori') }}"
                       placeholder="Contoh: Toples Kue, Gas LPG, Listrik Pabrik"
                       required>
                @error('nama_kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="4" 
                          placeholder="Deskripsi atau keterangan kategori BOP ini">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="reset" class="btn btn-light">Reset</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
