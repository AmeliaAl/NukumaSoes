@extends('adminlte::page')

@section('title', 'Overhead')

@section('content_header')
    <h1>Data Overhead</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">

        <a href="{{ route('overhead.create') }}"
           class="btn btn-primary">

            + Tambah Overhead

        </a>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Akun Overhead</th>
                    <th>Akun Pembayaran</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                @foreach($overheads as $overhead)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $overhead->tanggal }}
                    </td>

                    <td>
                        {{ $overhead->coa->nama_akun }}
                    </td>

                    <td>
                        {{ optional($overhead->paymentCoa)->nama_akun ?? '-' }}
                    </td>

                    <td>
                        {{ $overhead->keterangan }}
                    </td>

                    <td>

                        Rp {{ number_format($overhead->nominal, 0, ',', '.') }}

                    </td>

                    <td>

                        <a href="{{ route('overhead.edit', $overhead->id) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form action="{{ route('overhead.destroy', $overhead->id) }}"
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