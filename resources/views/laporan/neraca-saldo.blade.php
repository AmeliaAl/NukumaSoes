@extends('layouts.app')

@section('title', 'Laporan Neraca Saldo')
@section('page-title', 'Laporan Neraca Saldo')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1">📋 Laporan Neraca Saldo (Trial Balance)</h1>
        <p class="text-muted mb-0">Menampilkan saldo debit dan kredit dari seluruh akun buku besar secara real-time</p>
    </div>

    <!-- Trial Balance Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="15%" class="text-center">Kode Akun</th>
                            <th width="45%">Nama Rekening Akun</th>
                            <th width="20%" class="text-end">Debet (Rp)</th>
                            <th width="20%" class="text-end">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($neracaData as $data)
                        <tr>
                            <td class="text-center font-monospace"><strong>{{ $data->kode_akun }}</strong></td>
                            <td>{{ $data->nama_akun }}</td>
                            <td class="text-end">
                                @if($data->debit > 0)
                                    Rp {{ number_format($data->debit, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end">
                                @if($data->kredit > 0)
                                    Rp {{ number_format($data->kredit, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Tidak ada data akun</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light font-weight-bold">
                        <tr class="border-top border-dark border-2">
                            <td colspan="2" class="text-center text-uppercase"><strong>Total Neraca Saldo</strong></td>
                            <td class="text-end text-primary">
                                <strong>Rp {{ number_format($totalDebit, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end text-primary">
                                <strong>Rp {{ number_format($totalKredit, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Balanced Warning Alert -->
            @if(abs($totalDebit - $totalKredit) < 0.01)
                <div class="alert alert-success mt-4 mb-0 d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fa-2x"></i>
                    <div>
                        <strong>Buku Besar Seimbang (Balanced)!</strong> Total nilai saldo Debit dan saldo Kredit sama persis. Aliran dana pembukuan Anda secara matematis 100% konsisten.
                    </div>
                </div>
            @else
                <div class="alert alert-danger mt-4 mb-0 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                    <div>
                        <strong>Buku Besar Tidak Seimbang!</strong> Ada selisih sebesar Rp {{ number_format(abs($totalDebit - $totalKredit), 0, ',', '.') }} antara total Debit dan Kredit. Harap verifikasi jurnal transaksi Anda.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
