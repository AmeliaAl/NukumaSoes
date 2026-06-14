@extends('adminlte::page')

@section('title', 'Tambah Bahan Baku')

@section('content_header')
    <h1>Tambah Bahan Baku</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('bahan-baku.store') }}" method="POST">

            @csrf

            <div class="form-group">

                <label>Nama Bahan</label>

                <input type="text"
                       name="nama_bahan"
                       class="form-control"
                       required>

            </div>

            <div class="form-group">

                <label>Satuan</label>

                <input type="text"
                       name="satuan"
                       class="form-control"
                       required>

            </div>

            <button type="submit"
                    class="btn btn-success">

                Simpan

            </button>

            <a href="{{ url('bahan-baku') }}"
            class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>

@stop