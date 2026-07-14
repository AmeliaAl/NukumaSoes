@extends('adminlte::page')

@section('title', 'Bahan Baku')

@section('content_header')
    <h1>Data Bahan Baku</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">
        <a href="{{ route('bahan-baku.create') }}" class="btn btn-primary">
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
                    <th>Isi per Kemasan</th>
                    <th>Satuan</th>
                    <th>Jenis Bahan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bahanBakus as $bahan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $bahan->kode_bahan }}</td>
                    <td>{{ $bahan->nama_bahan }}</td>
                    <td>{{ $bahan->isi_per_kemasan ?? '-' }}</td>
                    <td>{{ $bahan->satuan }}</td>
                    <td>{{ $bahan->jenis_bahan }}</td>
                    <td>
                        <a href="{{ route('bahan-baku.edit', $bahan->id) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>
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
        ordering: false,
        stateSave: true
    });
});
</script>
@stop
