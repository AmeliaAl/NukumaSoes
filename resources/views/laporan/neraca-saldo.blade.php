@extends('layouts.app')

@section('title', 'Laporan Neraca Lajur')
@section('page-title', 'Laporan Neraca Lajur')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">📋 Laporan Neraca Lajur (Worksheet)</h1>
            <p class="text-muted mb-0">Kertas kerja 10 kolom pembantu penyusunan laporan keuangan HPP & Laba Rugi</p>
        </div>
        <!-- Filter Periode -->
        <div class="mt-3 mt-md-0">
            <form method="GET" action="{{ route('laporan.neraca-saldo') }}" class="row g-2 align-items-center" id="filterForm">
                <div class="col-auto">
                    <label for="periode" class="col-form-label fw-bold">Periode:</label>
                </div>
                <div class="col-auto">
                    <input type="month" name="periode" id="periode" value="{{ $periode }}" class="form-control" onchange="document.getElementById('filterForm').submit()">
                </div>
            </form>
        </div>
    </div>

    <!-- Worksheet Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            @php
                $parts = explode('-', $periode);
                $bulanNum = (int) ($parts[1] ?? date('m'));
                $tahunNum = $parts[0] ?? date('Y');
                $bulanList = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                $namaPeriode = $bulanList[$bulanNum] . ' ' . $tahunNum;
            @endphp
            
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1 uppercase">NUKUMA SOES</h4>
                <h5 class="fw-bold text-muted mb-1">NERACA LAJUR (WORKSHEET)</h5>
                <small class="text-muted text-uppercase fw-semibold">PERIODE: {{ $namaPeriode }}</small>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size: 13px;">
                    <thead class="table-light align-middle text-center">
                        <tr>
                            <th rowspan="2" width="7%">Kode</th>
                            <th rowspan="2" width="23%">Nama Akun</th>
                            <th colspan="2" width="14%">Neraca Saldo</th>
                            <th colspan="2" width="14%">Penyesuaian</th>
                            <th colspan="2" width="14%">N.S.D</th>
                            <th colspan="2" width="14%">Laba Rugi</th>
                            <th colspan="2" width="14%">Neraca</th>
                        </tr>
                        <tr>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($neracaData as $data)
                        <tr>
                            <td class="text-center font-monospace"><strong>{{ $data->kode_akun }}</strong></td>
                            <td>{{ $data->nama_akun }}</td>
                            <!-- Neraca Saldo -->
                            <td class="text-end">
                                {{ $data->ns_debit > 0 ? 'Rp ' . number_format($data->ns_debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end">
                                {{ $data->ns_kredit > 0 ? 'Rp ' . number_format($data->ns_kredit, 0, ',', '.') : '-' }}
                            </td>
                            <!-- Penyesuaian -->
                            <td class="text-end text-muted">
                                {{ $data->peny_debit > 0 ? 'Rp ' . number_format($data->peny_debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-muted">
                                {{ $data->peny_kredit > 0 ? 'Rp ' . number_format($data->peny_kredit, 0, ',', '.') : '-' }}
                            </td>
                            <!-- NSD -->
                            <td class="text-end fw-semibold">
                                {{ $data->nsd_debit > 0 ? 'Rp ' . number_format($data->nsd_debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-semibold">
                                {{ $data->nsd_kredit > 0 ? 'Rp ' . number_format($data->nsd_kredit, 0, ',', '.') : '-' }}
                            </td>
                            <!-- Laba Rugi -->
                            <td class="text-end text-success">
                                {{ $data->lr_debit > 0 ? 'Rp ' . number_format($data->lr_debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-success">
                                {{ $data->lr_kredit > 0 ? 'Rp ' . number_format($data->lr_kredit, 0, ',', '.') : '-' }}
                            </td>
                            <!-- Neraca -->
                            <td class="text-end text-primary">
                                {{ $data->n_debit > 0 ? 'Rp ' . number_format($data->n_debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-primary">
                                {{ $data->n_kredit > 0 ? 'Rp ' . number_format($data->n_kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted">Tidak ada data akun</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <!-- Subtotal Jumlah -->
                        <tr class="border-top border-dark border-1">
                            <td colspan="2" class="text-center text-uppercase"><strong>Jumlah</strong></td>
                            <!-- Neraca Saldo -->
                            <td class="text-end">Rp {{ number_format($totalNsDebit, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($totalNsKredit, 0, ',', '.') }}</td>
                            <!-- Penyesuaian -->
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <!-- NSD -->
                            <td class="text-end">Rp {{ number_format($totalNsdDebit, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($totalNsdKredit, 0, ',', '.') }}</td>
                            <!-- Laba Rugi -->
                            <td class="text-end text-success">Rp {{ number_format($totalLrDebit, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($totalLrKredit, 0, ',', '.') }}</td>
                            <!-- Neraca -->
                            <td class="text-end text-primary">Rp {{ number_format($totalNDebit, 0, ',', '.') }}</td>
                            <td class="text-end text-primary">Rp {{ number_format($totalNKredit, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Selisih Laba / Rugi Bersih -->
                        @if($labaBersih > 0)
                        <tr class="table-success">
                            <td colspan="2" class="text-center text-uppercase"><strong>Laba Bersih</strong></td>
                            <td colspan="6"></td>
                            <!-- Laba Rugi: Ditambahkan ke Debit untuk menyeimbangkan -->
                            <td class="text-end text-success">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                            <td class="text-end">-</td>
                            <!-- Neraca: Ditambahkan ke Kredit untuk menyeimbangkan -->
                            <td class="text-end">-</td>
                            <td class="text-end text-success">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                        </tr>
                        @elseif($rugiBersih > 0)
                        <tr class="table-danger">
                            <td colspan="2" class="text-center text-uppercase"><strong>Rugi Bersih</strong></td>
                            <td colspan="6"></td>
                            <!-- Laba Rugi: Ditambahkan ke Kredit untuk menyeimbangkan -->
                            <td class="text-end">-</td>
                            <td class="text-end text-danger">Rp {{ number_format($rugiBersih, 0, ',', '.') }}</td>
                            <!-- Neraca: Ditambahkan ke Debit untuk menyeimbangkan -->
                            <td class="text-end text-danger">Rp {{ number_format($rugiBersih, 0, ',', '.') }}</td>
                            <td class="text-end">-</td>
                        </tr>
                        @endif

                        <!-- Total Akhir yang Seimbang -->
                        <tr class="border-top border-dark border-2 bg-secondary bg-opacity-10">
                            <td colspan="2" class="text-center text-uppercase"><strong>Total Akhir</strong></td>
                            <!-- Neraca Saldo -->
                            <td class="text-end">Rp {{ number_format($totalNsDebit, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($totalNsKredit, 0, ',', '.') }}</td>
                            <!-- Penyesuaian -->
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <!-- NSD -->
                            <td class="text-end">Rp {{ number_format($totalNsdDebit, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($totalNsdKredit, 0, ',', '.') }}</td>
                            <!-- Laba Rugi -->
                            <td class="text-end text-success">
                                Rp {{ number_format($totalLrDebit + ($labaBersih > 0 ? $labaBersih : 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end text-success">
                                Rp {{ number_format($totalLrKredit + ($rugiBersih > 0 ? $rugiBersih : 0), 0, ',', '.') }}
                            </td>
                            <!-- Neraca -->
                            <td class="text-end text-primary">
                                Rp {{ number_format($totalNDebit + ($rugiBersih > 0 ? $rugiBersih : 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end text-primary">
                                Rp {{ number_format($totalNKredit + ($labaBersih > 0 ? $labaBersih : 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Balanced Info Alert -->
            @if(abs(($totalLrDebit + ($labaBersih > 0 ? $labaBersih : 0)) - ($totalLrKredit + ($rugiBersih > 0 ? $rugiBersih : 0))) < 0.01)
                <div class="alert alert-success mt-4 mb-0 d-flex align-items-center shadow-sm">
                    <i class="fas fa-check-circle me-3 fa-2x"></i>
                    <div>
                        <strong>Neraca Lajur Seimbang (Balanced)!</strong> Pengklasifikasian akun Laba Rugi dan Neraca telah selesai dan seimbang dengan status 
                        <strong>{{ $labaBersih > 0 ? 'Laba Bersih' : ($rugiBersih > 0 ? 'Rugi Bersih' : 'Simbang/Nihil') }}</strong>.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
