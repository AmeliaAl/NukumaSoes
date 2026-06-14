@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detail Jurnal Umum</h1>
            <p class="text-muted mb-0">No. Bukti: {{ $jurnal->nomor_bukti }}</p>
        </div>
        <div>
            <a href="{{ route('jurnal-umum.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Informasi Jurnal</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Tanggal</div>
                        <div class="col-sm-8 fw-bold">{{ $jurnal->tanggal->format('d F Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Nomor Bukti</div>
                        <div class="col-sm-8 fw-bold">{{ $jurnal->nomor_bukti }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Keterangan</div>
                        <div class="col-sm-8">{{ $jurnal->keterangan }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Dibuat Oleh</div>
                        <div class="col-sm-8">{{ $jurnal->admin->nama_admin }}</div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-sm-4 text-muted">Referensi Transaksi</div>
                        <div class="col-sm-8">
                            @if($jurnal->id_referensi)
                                <a href="{{ $routeReferensi }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Transaksi Asal
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Rincian Debit/Kredit</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Akun</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end pe-4">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalDebit = 0; $totalKredit = 0; @endphp
                            @foreach($jurnal->detail as $detail)
                            <tr>
                                <td class="ps-4 {{ $detail->kredit > 0 ? 'ps-5' : '' }}">
                                    <strong>{{ $detail->akun->kode_akun }}</strong> - {{ $detail->akun->nama_akun }}
                                </td>
                                <td class="text-end">
                                    @if($detail->debit > 0)
                                        Rp {{ number_format($detail->debit, 0, ',', '.') }}
                                        @php $totalDebit += $detail->debit; @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($detail->kredit > 0)
                                        Rp {{ number_format($detail->kredit, 0, ',', '.') }}
                                        @php $totalKredit += $detail->kredit; @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="ps-4">TOTAL</td>
                                <td class="text-end">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="text-end pe-4">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="alert alert-info border-0 shadow-sm">
                <h6><i class="fas fa-info-circle me-2"></i>Catatan Akuntansi</h6>
                <p class="small mb-0">
                    Jurnal ini dibuat secara otomatis oleh sistem sebagai bagian dari alur kerja produksi (Job Order Costing). 
                    Setiap perubahan pada transaksi terkait akan memperbarui atau membatalkan jurnal ini.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
