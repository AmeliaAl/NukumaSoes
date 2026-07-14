@extends('adminlte::page')

@section('title', 'Edit Supplier')

@section('content_header')
    <h1>Edit Supplier</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('supplier.update', $supplier->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="form-group">

                <label>Nama Supplier</label>

                <input type="text"
                       name="nama_supplier"
                       class="form-control"
                       value="{{ old('nama_supplier', $supplier->nama_supplier) }}">

                @error('nama_supplier')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <div class="form-group">

                <label>Alamat</label>

                <textarea name="alamat"
                          class="form-control">{{ old('alamat', $supplier->alamat) }}</textarea>

                @error('alamat')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <div class="form-group">

                <label>Telepon</label>

                <input type="text"
                       name="telepon"
                       class="form-control"
                       placeholder="08xxxxxxxxxx"
                       value="{{ old('telepon', $supplier->telepon) }}">

                @error('telepon')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <button type="submit"
                    class="btn btn-success">

                Update

            </button>

            <a href="{{ url('supplier') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>

@stop