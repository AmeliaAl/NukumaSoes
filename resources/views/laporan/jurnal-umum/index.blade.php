@extends('layouts.app')

@section('title', 'Laporan Jurnal Umum')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Jurnal Umum Produksi</h1>
            <p class="text-muted mb-0">Daftar transaksi akuntansi departemen produksi secara kronologis</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('jurnal-umum.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('jurnal-umum.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 120px;">Tanggal</th>
                            <th class="text-center" style="width: 150px;">No. Bukti</th>
                            <th>Keterangan / Akun</th>
                            <th class="text-center" style="width: 100px;">Ref</th>
                            <th class="text-end" style="width: 150px;">Debit</th>
                            <th class="text-end" style="width: 150px;">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jurnals as $jurnal)
                            <tr class="table-secondary">
                                <td class="text-center">{{ $jurnal->tanggal->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('jurnal-umum.show', $jurnal->id_jurnal) }}" class="fw-bold">
                                        {{ $jurnal->nomor_bukti }}
                                    </a>
                                </td>
                                <td colspan="4"><strong>{{ $jurnal->keterangan }}</strong></td>
                            </tr>
                            @foreach($jurnal->detail as $detail)
                            <tr>
                                <td colspan="2"></td>
                                <td class="{{ $detail->kredit > 0 ? 'ps-5' : '' }}">
                                    {{ $detail->akun->kode_akun }} - {{ $detail->akun->nama_akun }}
                                </td>
                                <td class="text-center">{{ $detail->akun->kode_akun }}</td>
                                <td class="text-end">
                                    {{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end">
                                    {{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada data jurnal untuk periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $jurnals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
