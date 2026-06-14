@extends('adminlte::page')

@section('title', 'Edit COA')

@section('content_header')
    <h1>Edit COA</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('coa.update', $coa->id) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="form-group">

                <label>Kode Akun</label>

                <input type="text"
                       name="kode_akun"
                       class="form-control"
                       value="{{ $coa->kode_akun }}">

            </div>

            <div class="form-group">

                <label>Nama Akun</label>

                <input type="text"
                       name="nama_akun"
                       class="form-control"
                       value="{{ $coa->nama_akun }}">

            </div>

            <div class="form-group">

                <label>Tipe Akun</label>

                <select name="tipe_akun" class="form-control">

                    <option value="Aktiva Lancar"
                        {{ $coa->tipe_akun == 'Aktiva Lancar' ? 'selected' : '' }}>
                        Aktiva Lancar
                    </option>

                    <option value="Kewajiban Lancar"
                        {{ $coa->tipe_akun == 'Kewajiban Lancar' ? 'selected' : '' }}>
                        Kewajiban Lancar
                    </option>

                    <option value="Pemakaian Bahan Baku"
                        {{ $coa->tipe_akun == 'Pemakaian Bahan Baku' ? 'selected' : '' }}>
                        Pemakaian Bahan Baku
                    </option>

                    <option value="Overhead Produksi"
                        {{ $coa->tipe_akun == 'Overhead Produksi' ? 'selected' : '' }}>
                        Overhead Produksi
                    </option>

                    <option value="Biaya Beban Operasional Umum"
                        {{ $coa->tipe_akun == 'Biaya Beban Operasional Umum' ? 'selected' : '' }}>
                        Biaya Beban Operasional Umum
                    </option>



                </select>

            </div>

            <button type="submit"
                    class="btn btn-success">

                Update

            </button>

            <a href="{{ url('coa') }}"
            class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>

@stop