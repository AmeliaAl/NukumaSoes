@extends('adminlte::page')

@section('title', 'Edit Bahan Baku')

@section('content_header')
    <h1>Edit Bahan Baku</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('bahan-baku.update', $bahanBaku->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Kode Bahan (readonly) --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Bahan</label>
                        <input type="text" class="form-control"
                               value="{{ $bahanBaku->kode_bahan }}" readonly disabled>
                    </div>
                </div>
            </div>

            {{-- Nama Bahan --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Bahan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bahan"
                               class="form-control @error('nama_bahan') is-invalid @enderror"
                               value="{{ old('nama_bahan', $bahanBaku->nama_bahan) }}" required>
                        @error('nama_bahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Isi per Kemasan --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Isi per Kemasan</label>
                        <input type="text" name="isi_per_kemasan" class="form-control"
                               value="{{ old('isi_per_kemasan', $bahanBaku->isi_per_kemasan) }}"
                               placeholder="Contoh: 25 (boleh kosong)">
                        <small class="form-text text-muted">Boleh kosong jika bahan bukan bahan kemasan.</small>
                    </div>
                </div>
            </div>

            {{-- Satuan --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="satuan"
                               class="form-control @error('satuan') is-invalid @enderror"
                               value="{{ old('satuan', $bahanBaku->satuan) }}" required>
                        @error('satuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Jenis Bahan (terakhir) --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Bahan <span class="text-danger">*</span></label>
                        <select name="jenis_bahan"
                                class="form-control @error('jenis_bahan') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Langsung"
                                {{ old('jenis_bahan', $bahanBaku->jenis_bahan) === 'Langsung' ? 'selected' : '' }}>
                                Langsung
                            </option>
                            <option value="Tidak Langsung"
                                {{ old('jenis_bahan', $bahanBaku->jenis_bahan) === 'Tidak Langsung' ? 'selected' : '' }}>
                                Tidak Langsung
                            </option>
                        </select>
                        @error('jenis_bahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Stok (readonly) --}}
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ url('bahan-baku') }}" class="btn btn-secondary">Cancel</a>

        </form>

    </div>
</div>

@stop
