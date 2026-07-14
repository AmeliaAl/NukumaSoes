@extends('adminlte::page')

@section('title', 'Laporan Overhead')

@section('content_header')
    <h1>Laporan Overhead</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <div class="text-center mb-3">
            <h4 class="font-weight-bold">LAPORAN OVERHEAD</h4>
            <p class="mb-0">
                Periode:
                {{ request('tanggal_awal') ? \Carbon\Carbon::parse(request('tanggal_awal'))->format('d/m/Y') : '-' }}
                s/d
                {{ request('tanggal_akhir') ? \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d/m/Y') : '-' }}
            </p>
        </div>

        <form method="GET" class="mb-3">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label>Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control"
                           value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control"
                           value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-3">
                    <label>Jenis Periode</label>
                    <select name="jenis_periode" class="form-control">
                        <option value="">Semua</option>
                        <option value="harian"   {{ request('jenis_periode') === 'harian'   ? 'selected' : '' }}>Harian</option>
                        <option value="mingguan" {{ request('jenis_periode') === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan"  {{ request('jenis_periode') === 'bulanan'  ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mr-1">Filter</button>
                    <a href="{{ url('laporan-overhead/print') }}?tanggal_awal={{ request('tanggal_awal') }}&tanggal_akhir={{ request('tanggal_akhir') }}&jenis_periode={{ request('jenis_periode') }}"
                       class="btn btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </form>

        <hr>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Tanggal</th>
                        <th>Jenis Periode</th>
                        <th>Periode Pembebanan</th>
                        <th>Akun Overhead</th>
                        <th>Keterangan</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $labelPeriode = ['harian'=>'Harian','mingguan'=>'Mingguan','bulanan'=>'Bulanan'];
                        $no = 1;
                    @endphp
                    @forelse($overheads as $overhead)
                        @php
                            $rowspan  = max(1, $overhead->details->count());
                            $jenis    = $overhead->jenis_periode ?? 'harian';
                            $periodeStr = \App\Http\Controllers\LaporanOverheadController::formatPeriode($overhead);
                        @endphp
                        <tr>
                            <td rowspan="{{ $rowspan }}" class="text-center align-middle">{{ $no++ }}</td>
                            <td rowspan="{{ $rowspan }}" class="align-middle">
                                {{ \Carbon\Carbon::parse($overhead->tanggal)->format('d/m/Y') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" class="align-middle">
                                {{ $labelPeriode[$jenis] ?? ucfirst($jenis) }}
                            </td>
                            <td rowspan="{{ $rowspan }}" class="align-middle">
                                {{ $periodeStr }}
                            </td>

                            @if($overhead->details->count() > 0)
                                <td>{{ $overhead->details[0]->coa->nama_akun ?? '-' }}</td>
                                <td>{{ $overhead->details[0]->keterangan }}</td>
                                <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($overhead->details[0]->nominal) }}</td>
                            @else
                                <td>{{ $overhead->coa->nama_akun ?? '-' }}</td>
                                <td>{{ $overhead->keterangan }}</td>
                                <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($overhead->nominal) }}</td>
                            @endif
                        </tr>
                        @for($i = 1; $i < $rowspan; $i++)
                        <tr>
                            <td>{{ $overhead->details[$i]->coa->nama_akun ?? '-' }}</td>
                            <td>{{ $overhead->details[$i]->keterangan }}</td>
                            <td class="text-right">{{ \App\Helpers\FormatHelper::rupiah($overhead->details[$i]->nominal) }}</td>
                        </tr>
                        @endfor
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                Tidak ada transaksi overhead untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($overheads->count() > 0)
                <tfoot>
                    <tr class="table-active">
                        <th colspan="6" class="text-right font-weight-bold">TOTAL</th>
                        <th class="text-right font-weight-bold">{{ \App\Helpers\FormatHelper::rupiah($total) }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

    </div>
</div>

@stop

