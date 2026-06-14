@extends('adminlte::page')

@section('title', 'Edit Overhead')

@section('content_header')
    <h1>Edit Overhead</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('overhead.update', $overhead->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="form-group">

                <label>Tanggal</label>

                <input type="date"
                       name="tanggal"
                       class="form-control"
                       value="{{ $overhead->tanggal }}">

            </div>

            <div class="form-group">

                <label>Akun Overhead</label>

                <select name="coa_id"
                        class="form-control">

                    @foreach($coas as $coa)

                    <option value="{{ $coa->id }}"
                        {{ $overhead->coa_id == $coa->id ? 'selected' : '' }}>

                        {{ $coa->kode_akun }} - {{ $coa->nama_akun }}

                    </option>

                    @endforeach

                </select>

            </div>

            <div class="form-group">

                <label>Akun Pembayaran</label>

                <select name="coa_pembayaran_id"
                        class="form-control">

                    @foreach($paymentCoas as $coa)

                    <option value="{{ $coa->id }}"
                        {{ $overhead->coa_pembayaran_id == $coa->id ? 'selected' : '' }}>

                        {{ $coa->kode_akun }} - {{ $coa->nama_akun }}

                    </option>

                    @endforeach

                </select>

            </div>

            <div class="form-group">

                <label>Keterangan</label>

                <textarea name="keterangan"
                          class="form-control"
                          rows="3">{{ $overhead->keterangan }}</textarea>

            </div>

            <div class="form-group">

                <label>Nominal</label>

                <input type="text"
                       name="nominal"
                       id="nominal"
                       class="form-control"
                       value="{{ number_format($overhead->nominal, 0, ',', '.') }}">

            </div>

            <button type="submit"
                    class="btn btn-success">

                Update

            </button>

            <a href="{{ url('overhead') }}"
            class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>

@stop

@section('js')

<script>

    const nominalInput = document.getElementById('nominal');

    nominalInput.addEventListener('input', function() {

        let value = this.value.replace(/\D/g, '');

        this.value = new Intl.NumberFormat('id-ID').format(value);

    });

</script>

@stop