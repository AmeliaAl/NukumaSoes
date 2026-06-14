@extends('adminlte::page')

@section('title', 'Bahan Baku')

@section('content_header')
    <h1>Data Bahan Baku</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">

        <a href="{{ route('bahan-baku.create') }}"
           class="btn btn-primary">

            + Tambah Bahan Baku

        </a>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Kode Bahan</th>
                    <th>Nama Bahan</th>
                    <th>Satuan</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                @foreach($bahanBakus as $bahan)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $bahan->kode_bahan }}
                    </td>

                    <td>
                        {{ $bahan->nama_bahan }}
                    </td>

                    <td>
                        {{ $bahan->satuan }}
                    </td>

                    <td>

                        <a href="{{ route('bahan-baku.edit', $bahan->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ route('bahan-baku.destroy', $bahan->id) }}"
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