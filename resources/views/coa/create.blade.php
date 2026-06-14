@extends('adminlte::page')

@section('title', 'Tambah COA')

@section('content_header')
    <h1>Tambah COA</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('coa.store') }}" method="POST">

            @csrf

            <div class="form-group">

                <label>Kode Akun</label>

                <input type="text"
                       name="kode_akun"
                       class="form-control">

            </div>

            <div class="form-group">

                <label>Nama Akun</label>

                <input type="text"
                       name="nama_akun"
                       class="form-control">

            </div>

            <div class="form-group">

                <label>Tipe Akun</label>

                <select name="tipe_akun" class="form-control">

                    <option value="">
                        -- Pilih Tipe Akun --
                    </option>

                    <option value="Aktiva Lancar">
                        Aktiva Lancar
                    </option>

                    <option value="Kewajiban Lancar">
                        Kewajiban Lancar
                    </option>

                    <option value="Pemakaian Bahan Baku">
                        Pemakaian Bahan Baku
                    </option>

                    <option value="Overhead Produksi">
                        Overhead Produksi
                    </option>

                    <option value="Biaya Beban Operasional Umum">
                    Biaya Beban Operasional Umum
                    </option>

                </select>

            </div>

            <button type="submit" class="btn btn-success">
                Simpan
            </button>

            <a href="{{ url('coa') }}"
            class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>

@stop