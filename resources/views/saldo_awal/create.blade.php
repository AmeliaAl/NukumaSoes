@extends('adminlte::page')

@section('title', 'Tambah Setoran Modal Awal')

@section('content_header')
    <h1>Tambah Setoran Modal Awal</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        {{-- Pesan info jika sudah ada periode awal --}}
        @if($periodeAwal)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Periode Saldo Awal Terbatas</strong><br>
            Saldo Awal hanya dapat diinput pada periode awal penggunaan aplikasi. 
            <strong>Seluruh akun harus menggunakan periode saldo awal yang sama.</strong><br>
            <strong style="color:#0056b3;">Periode yang digunakan: {{ \Carbon\Carbon::parse($periodeAwal)->translatedFormat('d F Y') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <form action="{{ route('saldo-awal.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        @if($periodeAwal)
                            {{-- Input tanggal readonly jika sudah ada periode awal --}}
                            <input type="date" name="tanggal"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   required readonly 
                                   value="{{ $periodeAwal }}"
                                   title="Tanggal harus sama dengan periode saldo awal pertama">
                            <small class="form-text text-muted">Tanggal tidak dapat diubah. Harus sama dengan periode awal aplikasi.</small>
                        @else
                            {{-- Input tanggal normal untuk periode pertama --}}
                            <input type="date" name="tanggal"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   required value="{{ old('tanggal', date('Y-m-d')) }}"
                                   title="Pilih tanggal untuk periode awal">
                            <small class="form-text text-muted">Tanggal ini akan menjadi periode saldo awal aplikasi.</small>
                        @endif
                        @error('tanggal')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun <span class="text-danger">*</span></label>
                        <select name="coa_id"
                                class="form-control @error('coa_id') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->id }}"
                                {{ old('coa_id') == $coa->id ? 'selected' : '' }}>
                                {{ $coa->no_akun }} - {{ $coa->nama_akun }}
                            </option>
                            @endforeach
                        </select>
                        @error('coa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nominal Saldo Awal <span class="text-danger">*</span></label>
                        <input type="text" name="nominal" id="nominal"
                               class="form-control @error('nominal') is-invalid @enderror"
                               placeholder="0" required
                               value="{{ old('nominal') }}">
                        @error('nominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
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
