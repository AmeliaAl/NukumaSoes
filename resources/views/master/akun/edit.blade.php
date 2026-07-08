@extends('layouts.app')

@section('title', 'Edit Akun')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Akun</h1>
            <p class="text-muted mb-0">Ubah master data akun</p>
        </div>
        <div>
            <a href="{{ route('akun.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('akun.update', $akun->id_akun) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Akun <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode_akun') is-invalid @enderror" 
                                       name="kode_akun" value="{{ old('kode_akun', $akun->kode_akun) }}" required>
                                @error('kode_akun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Akun <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_akun') is-invalid @enderror" 
                                       name="nama_akun" value="{{ old('nama_akun', $akun->nama_akun) }}" required>
                                @error('nama_akun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipe Akun <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipe_akun') is-invalid @enderror" name="tipe_akun" required>
                                    <option value="">-- Pilih Tipe Akun --</option>
                                    @foreach($tipeAkun as $key => $label)
                                        <option value="{{ $key }}" {{ old('tipe_akun', $akun->tipe_akun) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipe_akun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Saldo Normal <span class="text-danger">*</span></label>
                                <select class="form-select @error('saldo_normal') is-invalid @enderror" name="saldo_normal" required>
                                    <option value="">-- Pilih Saldo Normal --</option>
                                    <option value="debit" {{ old('saldo_normal', $akun->saldo_normal) == 'debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="kredit" {{ old('saldo_normal', $akun->saldo_normal) == 'kredit' ? 'selected' : '' }}>Kredit</option>
                                </select>
                                @error('saldo_normal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="aktif" {{ old('status', $akun->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $akun->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      name="keterangan" rows="3">{{ old('keterangan', $akun->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
