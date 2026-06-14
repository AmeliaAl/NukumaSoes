@extends('adminlte::page')

@section('title', 'Supplier')

@section('content_header')
    <h1>Data Supplier</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">

        <a href="{{ route('supplier.create') }}"
           class="btn btn-primary">

            + Tambah Supplier

        </a>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>ID Supplier</th>
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                @foreach($suppliers as $supplier)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $supplier->kode_supplier }}
                    </td>

                    <td>
                        {{ $supplier->nama_supplier }}
                    </td>

                    <td>
                        {{ $supplier->alamat }}
                    </td>

                    <td>
                        {{ $supplier->telepon }}
                    </td>

                    <td>

                        <a href="{{ route('supplier.edit', $supplier->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ route('supplier.destroy', $supplier->id) }}"
                              method="POST"
                              style="display:inline-block;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin hapus data?')">

                                Hapus

                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@stop

@section('js')

<script>

$(document).ready(function () {

    $('.table').DataTable({

        ordering: false

    });

});

</script>

@stop