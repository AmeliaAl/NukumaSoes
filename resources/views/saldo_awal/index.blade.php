@extends('adminlte::page')

@section('title', 'Setoran Modal Awal')

@section('content_header')
    <h1>Data Setoran Modal Awal</h1>
@stop

@section('plugins.Datatables', true)

@section('content')

<div class="card">

    <div class="card-header">
        @php
            $coaIdsAda = \App\Models\SaldoAwal::pluck('coa_id')->toArray();
            $sisaAkun  = \App\Models\Akun::whereIn('no_akun', ['111','112'])
                            ->whereNotIn('id', $coaIdsAda)->count();
        @endphp

        @if($sisaAkun > 0)
            <a href="{{ route('saldo-awal.create') }}" class="btn btn-primary">
                + Tambah Setoran Modal Awal
            </a>
        @else
            <span class="text-muted">
                <i class="fas fa-check-circle text-success mr-1"></i>
                Seluruh akun telah memiliki saldo awal.
            </span>
        @endif
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-info-circle mr-1"></i> {{ session('info') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Akun</th>
                    <th>Saldo Awal</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($saldoAwals as $saldoAwal)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $saldoAwal->coa->no_akun ?? '-' }} - {{ $saldoAwal->coa->nama_akun ?? '-' }}</td>
                    <td>{{ \App\Helpers\FormatHelper::rupiah($saldoAwal->nominal) }}</td>
                    <td>{{ \Carbon\Carbon::parse($saldoAwal->tanggal)->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        Belum ada data saldo awal.
                    </td>
                </tr>
                @endforelse
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
