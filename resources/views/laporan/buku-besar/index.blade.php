@extends('layouts.app')

@section('title', 'Laporan Buku Besar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Buku Besar (General Ledger)</h1>
            <p class="text-muted mb-0">Rincian mutasi setiap akun dalam periode tertentu</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('buku-besar.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pilih Akun <span class="text-danger">*</span></label>
                    <select name="id_akun" class="form-select select2" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach($akuns as $akun)
                            <option value="{{ $akun->id_akun }}" {{ request('id_akun') == $akun->id_akun ? 'selected' : '' }}>
                                {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedAkun)
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Mutasi Akun: <strong>{{ $selectedAkun->kode_akun }} - {{ $selectedAkun->nama_akun }}</strong>
            </h5>
            <span class="badge bg-info text-dark">Saldo Normal: {{ ucfirst($selectedAkun->saldo_normal) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 120px;">Tanggal</th>
                            <th class="text-center" style="width: 150px;">No. Bukti</th>
                            <th>Keterangan</th>
                            <th class="text-end" style="width: 150px;">Debit</th>
                            <th class="text-end" style="width: 150px;">Kredit</th>
                            <th class="text-end" style="width: 180px;">Saldo Kumulatif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Saldo Awal -->
                        <tr class="table-light italic">
                            <td colspan="3" class="text-end"><strong>SALDO AWAL ({{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }})</strong></td>
                            <td colspan="2"></td>
                            <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                        </tr>

                        @php $currentSaldo = $saldoAwal; @endphp
                        @forelse($jurnalDetails as $detail)
                            @php
                                if ($selectedAkun->saldo_normal == 'debit') {
                                    $currentSaldo += ($detail->debit - $detail->kredit);
                                } else {
                                    $currentSaldo += ($detail->kredit - $detail->debit);
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $detail->jurnalUmum->tanggal->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('jurnal-umum.show', $detail->id_jurnal) }}">
                                        {{ $detail->jurnalUmum->nomor_bukti }}
                                    </a>
                                </td>
                                <td>{{ $detail->jurnalUmum->keterangan }}</td>
                                <td class="text-end">
                                    {{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end">
                                    {{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($currentSaldo, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada mutasi pada periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">SALDO AKHIR</td>
                            <td class="text-end text-primary" style="font-size: 1.1rem;">Rp {{ number_format($currentSaldo, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-light border text-center py-5">
        <i class="fas fa-book fa-3x text-muted mb-3"></i>
        <h5>Silakan pilih akun dan periode tanggal untuk menampilkan buku besar.</h5>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endpush
