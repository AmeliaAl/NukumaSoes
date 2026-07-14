@extends('adminlte::page')

@section('title', 'COA')

@section('content_header')
    <h1>Chart Of Account (COA)</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">
        <a href="{{ route('coa.create') }}" class="btn btn-primary">
            + Tambah Akun
        </a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Akun</th>
                    <th>Nama Akun</th>
                    <th>Kelompok Akun</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coas as $coa)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $coa->no_akun }}</td>
                    <td>{{ $coa->nama_akun }}</td>
                    <td>{{ $coa->header_akun }}</td>
                    <td>
                        <a href="{{ route('coa.edit', $coa->id) }}" class="btn btn-warning btn-sm">
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
    $('.table').DataTable({ ordering: false, stateSave: true });
});
</script>
@stop
