@extends('layouts.app')

@section('title', 'Laporan Neraca Lajur')
@section('page-title', 'Laporan Neraca Lajur')

@push('styles')
<style>
    .neraca-lajur-table {
        font-size: 11px;
        min-width: 1200px;
    }
    .neraca-lajur-table thead th {
        vertical-align: middle;
        text-align: center;
        font-size: 10px;
        white-space: nowrap;
        padding: 6px 4px;
    }
    .neraca-lajur-table tbody td {
        padding: 4px 6px;
        white-space: nowrap;
        vertical-align: middle;
    }
    .neraca-lajur-table tfoot td {
        padding: 6px 4px;
        font-size: 11px;
    }
    .col-group-ns   { background-color: #f1f5f9; }
    .col-group-peny { background-color: #fef9c3; }
    .col-group-nsd  { background-color: #e0f2fe; }
    .col-group-lr   { background-color: #dcfce7; }
    .col-group-n    { background-color: #ede9fe; }

    .col-group-ns-td   { background-color: #f8fafc; }
    .col-group-peny-td { background-color: #fffde7; }
    .col-group-nsd-td  { background-color: #f0faff; }
    .col-group-lr-td   { background-color: #f0fdf4; }
    .col-group-n-td    { background-color: #f5f3ff; }

    .text-num { font-family: 'Courier New', monospace; }

    .laba-row td { background-color: #bbf7d0 !important; font-weight: bold; }
    .rugi-row td { background-color: #fee2e2 !important; font-weight: bold; }
    .total-row td { background-color: #dde1e7 !important; font-weight: bold; border-top: 2px solid #374151; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Header & Filter -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div>
            <h1 class="h4 mb-1">📋 Neraca Lajur <span class="text-muted fs-6 fw-normal">(Worksheet 10 Kolom)</span></h1>
            <p class="text-muted mb-0 small">Kertas kerja pembantu penyusunan HPP & Laporan Laba Rugi</p>
        </div>
        <form method="GET" action="{{ route('laporan.neraca-saldo') }}" id="filterForm" class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <label for="periode" class="form-label mb-0 fw-semibold text-nowrap">Periode:</label>
            <input type="month" name="periode" id="periode" value="{{ $periode }}"
                   class="form-control form-control-sm" style="width:160px;"
                   onchange="document.getElementById('filterForm').submit()">
        </form>
    </div>

    <!-- Title Card -->
    <div class="card shadow-sm mb-0">
        <div class="card-body py-2 text-center bg-light border-bottom">
            @php
                $parts    = explode('-', $periode);
                $bulanNum = (int)($parts[1] ?? date('m'));
                $tahunNum = $parts[0] ?? date('Y');
                $namaBln  = ['','Januari','Februari','Maret','April','Mei','Juni',
                             'Juli','Agustus','September','Oktober','November','Desember'];
                $namaPeriode = $namaBln[$bulanNum] . ' ' . $tahunNum;
            @endphp
            <div class="fw-bold text-uppercase" style="font-size:13px;">NUKUMA SOES</div>
            <div class="fw-bold text-uppercase text-secondary" style="font-size:12px;">NERACA LAJUR — PERIODE: {{ $namaPeriode }}</div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 neraca-lajur-table" id="tblLajur">
                    <thead>
                        <tr>
                            <th rowspan="2" width="5%" class="border-end">Kode</th>
                            <th rowspan="2" width="18%" class="border-end">Nama Akun</th>

                            <th colspan="2" class="col-group-ns border-end">Neraca Saldo</th>
                            <th colspan="2" class="col-group-peny border-end">Penyesuaian</th>
                            <th colspan="2" class="col-group-nsd border-end">N.S.D</th>
                            <th colspan="2" class="col-group-lr border-end">Laba Rugi</th>
                            <th colspan="2" class="col-group-n">Neraca</th>
                        </tr>
                        <tr>
                            <th class="col-group-ns">D</th>
                            <th class="col-group-ns border-end">K</th>
                            <th class="col-group-peny">D</th>
                            <th class="col-group-peny border-end">K</th>
                            <th class="col-group-nsd">D</th>
                            <th class="col-group-nsd border-end">K</th>
                            <th class="col-group-lr">D</th>
                            <th class="col-group-lr border-end">K</th>
                            <th class="col-group-n">D</th>
                            <th class="col-group-n">K</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($neracaData as $data)
                        <tr>
                            <td class="font-monospace fw-bold text-center border-end" style="font-size:10px;">{{ $data->kode_akun }}</td>
                            <td class="border-end" style="max-width:180px; overflow:hidden; text-overflow:ellipsis;" title="{{ $data->nama_akun }}">{{ $data->nama_akun }}</td>

                            {{-- Neraca Saldo --}}
                            <td class="text-end text-num col-group-ns-td">{{ $data->ns_debit  > 0 ? number_format($data->ns_debit,  0,',','.') : '-' }}</td>
                            <td class="text-end text-num col-group-ns-td border-end">{{ $data->ns_kredit > 0 ? number_format($data->ns_kredit, 0,',','.') : '-' }}</td>

                            {{-- Penyesuaian --}}
                            <td class="text-end text-num col-group-peny-td text-muted">{{ $data->peny_debit  > 0 ? number_format($data->peny_debit,  0,',','.') : '-' }}</td>
                            <td class="text-end text-num col-group-peny-td text-muted border-end">{{ $data->peny_kredit > 0 ? number_format($data->peny_kredit, 0,',','.') : '-' }}</td>

                            {{-- NSD --}}
                            <td class="text-end text-num col-group-nsd-td">{{ $data->nsd_debit  > 0 ? number_format($data->nsd_debit,  0,',','.') : '-' }}</td>
                            <td class="text-end text-num col-group-nsd-td border-end">{{ $data->nsd_kredit > 0 ? number_format($data->nsd_kredit, 0,',','.') : '-' }}</td>

                            {{-- Laba Rugi --}}
                            <td class="text-end text-num col-group-lr-td {{ $data->lr_debit  > 0 ? 'fw-semibold text-success' : '' }}">{{ $data->lr_debit  > 0 ? number_format($data->lr_debit,  0,',','.') : '-' }}</td>
                            <td class="text-end text-num col-group-lr-td border-end {{ $data->lr_kredit > 0 ? 'fw-semibold text-success' : '' }}">{{ $data->lr_kredit > 0 ? number_format($data->lr_kredit, 0,',','.') : '-' }}</td>

                            {{-- Neraca --}}
                            <td class="text-end text-num col-group-n-td {{ $data->n_debit  > 0 ? 'fw-semibold text-primary' : '' }}">{{ $data->n_debit  > 0 ? number_format($data->n_debit,  0,',','.') : '-' }}</td>
                            <td class="text-end text-num col-group-n-td  {{ $data->n_kredit > 0 ? 'fw-semibold text-primary' : '' }}">{{ $data->n_kredit > 0 ? number_format($data->n_kredit, 0,',','.') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted fst-italic">
                                Tidak ada data akun aktif untuk periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        {{-- Baris Jumlah --}}
                        <tr class="fw-bold" style="font-size:11px; border-top: 2px solid #374151;">
                            <td colspan="2" class="text-center text-uppercase border-end">Jumlah</td>
                            <td class="text-end text-num col-group-ns">{{ number_format($totalNsDebit,  0,',','.') }}</td>
                            <td class="text-end text-num col-group-ns  border-end">{{ number_format($totalNsKredit, 0,',','.') }}</td>
                            <td class="text-end text-num col-group-peny text-muted">-</td>
                            <td class="text-end text-num col-group-peny text-muted border-end">-</td>
                            <td class="text-end text-num col-group-nsd">{{ number_format($totalNsdDebit,  0,',','.') }}</td>
                            <td class="text-end text-num col-group-nsd border-end">{{ number_format($totalNsdKredit, 0,',','.') }}</td>
                            <td class="text-end text-num col-group-lr text-success">{{ number_format($totalLrDebit,  0,',','.') }}</td>
                            <td class="text-end text-num col-group-lr text-success border-end">{{ number_format($totalLrKredit, 0,',','.') }}</td>
                            <td class="text-end text-num col-group-n text-primary">{{ number_format($totalNDebit,  0,',','.') }}</td>
                            <td class="text-end text-num col-group-n text-primary">{{ number_format($totalNKredit, 0,',','.') }}</td>
                        </tr>

                        {{-- Baris Laba/Rugi Bersih --}}
                        @if($labaBersih > 0)
                        <tr class="laba-row" style="font-size:11px;">
                            <td colspan="2" class="text-center text-uppercase border-end">Laba Bersih</td>
                            <td colspan="6" class="border-end text-center text-muted fst-italic small">—</td>
                            <td class="text-end text-num">{{ number_format($labaBersih, 0,',','.') }}</td>
                            <td class="text-end text-num border-end">-</td>
                            <td class="text-end text-num">-</td>
                            <td class="text-end text-num">{{ number_format($labaBersih, 0,',','.') }}</td>
                        </tr>
                        @elseif($rugiBersih > 0)
                        <tr class="rugi-row" style="font-size:11px;">
                            <td colspan="2" class="text-center text-uppercase border-end">Rugi Bersih</td>
                            <td colspan="6" class="border-end text-center text-muted fst-italic small">—</td>
                            <td class="text-end text-num">-</td>
                            <td class="text-end text-num border-end">{{ number_format($rugiBersih, 0,',','.') }}</td>
                            <td class="text-end text-num">{{ number_format($rugiBersih, 0,',','.') }}</td>
                            <td class="text-end text-num">-</td>
                        </tr>
                        @endif

                        {{-- Baris Total Akhir --}}
                        <tr class="total-row" style="font-size:11px;">
                            <td colspan="2" class="text-center text-uppercase border-end">Total Akhir</td>
                            <td class="text-end text-num">{{ number_format($totalNsDebit,  0,',','.') }}</td>
                            <td class="text-end text-num border-end">{{ number_format($totalNsKredit, 0,',','.') }}</td>
                            <td class="text-end text-num text-muted">-</td>
                            <td class="text-end text-num text-muted border-end">-</td>
                            <td class="text-end text-num">{{ number_format($totalNsdDebit,  0,',','.') }}</td>
                            <td class="text-end text-num border-end">{{ number_format($totalNsdKredit, 0,',','.') }}</td>
                            <td class="text-end text-num">{{ number_format($totalLrDebit  + ($labaBersih > 0 ? $labaBersih : 0), 0,',','.') }}</td>
                            <td class="text-end text-num border-end">{{ number_format($totalLrKredit + ($rugiBersih > 0 ? $rugiBersih : 0), 0,',','.') }}</td>
                            <td class="text-end text-num">{{ number_format($totalNDebit  + ($rugiBersih > 0 ? $rugiBersih : 0), 0,',','.') }}</td>
                            <td class="text-end text-num">{{ number_format($totalNKredit + ($labaBersih > 0 ? $labaBersih : 0), 0,',','.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Status Seimbang --}}
        @if($labaBersih > 0 || $rugiBersih > 0 || ($totalNsDebit == $totalNsKredit))
        <div class="card-footer py-2">
            @if($labaBersih > 0)
            <div class="alert alert-success mb-0 py-2 d-flex align-items-center">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <span class="small"><strong>Neraca Lajur Seimbang!</strong> Laba Bersih periode ini: <strong>Rp {{ number_format($labaBersih, 0, ',', '.') }}</strong></span>
            </div>
            @elseif($rugiBersih > 0)
            <div class="alert alert-danger mb-0 py-2 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                <span class="small"><strong>Neraca Lajur Seimbang!</strong> Rugi Bersih periode ini: <strong>Rp {{ number_format($rugiBersih, 0, ',', '.') }}</strong></span>
            </div>
            @else
            <div class="alert alert-info mb-0 py-2 d-flex align-items-center">
                <i class="fas fa-info-circle me-2 text-info"></i>
                <span class="small">Tidak ada akun nominal (Laba Rugi) yang memiliki saldo pada periode ini.</span>
            </div>
            @endif
        </div>
        @endif
    </div>

    <p class="text-muted small mt-2">
        <i class="fas fa-info-circle"></i>
        <strong>Keterangan:</strong> Kolom Laba Rugi diisi otomatis oleh akun bertipe <span class="badge bg-success">Pendapatan</span> atau <span class="badge bg-warning text-dark">Beban</span>. Kolom Neraca diisi oleh akun bertipe <span class="badge bg-primary">Aset</span>, <span class="badge bg-secondary">Kewajiban</span>, dan <span class="badge bg-dark">Ekuitas</span>.
    </p>
</div>
@endsection
