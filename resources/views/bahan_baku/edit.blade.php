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

            <div class="form-group">

                <label>Nama Bahan</label>

                <input type="text"
                       name="nama_bahan"
                       class="form-control"
                       value="{{ $bahanBaku->nama_bahan }}"
                       required>

            </div>

            <div class="form-group">

                <label>Satuan</label>

                <input type="text"
                       name="satuan"
                       class="form-control"
                       value="{{ $bahanBaku->satuan }}"
                       required>

            </div>

            <button type="submit"
                    class="btn btn-success">

                Update

            </button>

            <a href="{{ url('bahan-baku') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>

@stop