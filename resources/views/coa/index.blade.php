@extends('adminlte::page')

@section('title', 'COA')

@section('content_header')
    <h1>Chart Of Account (COA)</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">

        <a href="{{ route('coa.create') }}"
           class="btn btn-primary">

            + Tambah Akun

        </a>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th>Tipe Akun</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                @foreach($coas as $coa)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $coa->kode_akun }}
                    </td>

                    <td>
                        {{ $coa->nama_akun }}
                    </td>

                    <td>
                        {{ $coa->tipe_akun }}
                    </td>

                    <td>

                        <a href="{{ route('coa.edit', $coa->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ route('coa.destroy', $coa->id) }}"
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