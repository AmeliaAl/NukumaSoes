@extends('adminlte::page')

@section('title', 'Edit Setoran Modal Awal')

@section('content_header')
    <h1>Edit Setoran Modal Awal</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('saldo-awal.update', $saldoAwal->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                               required value="{{ old('tanggal', $saldoAwal->tanggal) }}">
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No Bukti</label>
                        <input type="text" class="form-control" value="{{ $saldoAwal->no_bukti }}" readonly disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun <span class="text-danger">*</span></label>
                        <select name="coa_id" class="form-control @error('coa_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->id }}" {{ (old('coa_id', $saldoAwal->coa_id) == $coa->id) ? 'selected' : '' }}>
                                {{ $coa->no_akun }} - {{ $coa->nama_akun }}
                            </option>
                            @endforeach
                        </select>
                        @error('coa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nominal <span class="text-danger">*</span></label>
                        <input type="text" name="nominal" id="nominal"
                               class="form-control @error('nominal') is-invalid @enderror"
                               required value="{{ old('nominal', number_format($saldoAwal->nominal, 0, ',', '.')) }}">
                        @error('nominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                               value="{{ old('keterangan', $saldoAwal->keterangan) }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('saldo-awal.index') }}" class="btn btn-secondary">Cancel</a>

        </form>

    </div>
</div>

@stop

@section('js')
<script>
    function formatRupiah(value) {
        if (!value) return '';
        return new Intl.NumberFormat('id-ID').format(value.toString().replace(/\D/g, ''));
    }

    $(document).ready(function () {
        $('#nominal').on('input', function () {
            $(this).val(formatRupiah($(this).val()));
        });
    });
</script>
@stop
