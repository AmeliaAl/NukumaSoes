@extends('adminlte::page')

@section('title', 'Edit COA')

@section('content_header')
    <h1>Edit COA</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('coa.update', $coa->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>No Akun <span class="text-danger">*</span></label>
                <input type="text" name="no_akun"
                       class="form-control @error('no_akun') is-invalid @enderror"
                       value="{{ old('no_akun', $coa->no_akun) }}" required>
                @error('no_akun')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Nama Akun <span class="text-danger">*</span></label>
                <input type="text" name="nama_akun"
                       class="form-control @error('nama_akun') is-invalid @enderror"
                       value="{{ old('nama_akun', $coa->nama_akun) }}" required>
                @error('nama_akun')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Kelompok Akun <span class="text-danger">*</span></label>
                <select name="header_akun"
                        class="form-control @error('header_akun') is-invalid @enderror" required>
                    <option value="">-- Pilih Kelompok Akun --</option>
                    @foreach(['Aktiva Lancar','Kewajiban Lancar','Pemakaian Bahan Baku','Overhead Produksi','Biaya Beban Operasional Umum','Ekuitas'] as $h)
                    <option value="{{ $h }}" {{ old('header_akun', $coa->header_akun) === $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
                @error('header_akun')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ url('coa') }}" class="btn btn-secondary">Cancel</a>

        </form>

    </div>
</div>

@stop
