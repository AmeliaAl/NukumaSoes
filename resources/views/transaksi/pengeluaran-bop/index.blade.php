@extends('layouts.app')

@section('title', 'BOP Aktual')
@section('page-title', 'Pengeluaran BOP Aktual')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Pengeluaran BOP Aktual</h4>
            <p class="text-muted mb-0">Catat tagihan pengeluaran BOP yang sesungguhnya terjadi</p>
        </div>
        <a href="{{ route('pengeluaran-bop.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Input BOP Aktual
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">BOP Belum Diaktualkan</h5>
            <small class="text-muted">Pilih item untuk mengisi harga aktual dan periode tagihannya.</small>
        </div>
        <span class="badge bg-warning text-dark">{{ $bopBelumAktual->count() }} belum aktual</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal Anggaran</th>
                        <th>Jenis BOP</th>
                        <th>Job Order / Produk</th>
                        <th>Perhitungan</th>
                        <th class="text-end">Nominal Anggaran</th>
                        <th class="text-center">Input</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bopBelumAktual as $bop)
                    <tr>
                        <td>{{ $bop->tanggal_overhead->format('d/m/Y') }}</td>
                        <td><span class="badge bg-info text-dark">{{ $bop->jenis_overhead }}</span></td>
                        <td>
                            <strong>{{ $bop->permintaanProduksi->nomor_job ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $bop->permintaanProduksi->produk->nama_produk ?? '-' }}</small>
                        </td>
                        <td>{{ $bop->jumlah_batch }} {{ $bop->satuan_periode_label }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($bop->nominal, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('pengeluaran-bop.create', ['bop_id' => $bop->id_overhead]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-1"></i>Input Aktual
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Semua BOP sudah diaktualkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Riwayat BOP Aktual</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Periode Tagihan</th>
                        <th>Nomor Pembayaran</th>
                        <th>Nomor Bukti</th>
                        <th>Keterangan</th>
                        <th class="text-end">Nominal (Debit)</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnals as $jurnal)
                    <tr>
                        <td>{{ $jurnal->tanggal->format('d/m/Y') }}</td>
                        <td>
                            @if($jurnal->periode_mulai && $jurnal->periode_selesai)
                                {{ $jurnal->periode_mulai->format('d/m/Y') }} - {{ $jurnal->periode_selesai->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $jurnal->nomor_pembayaran ?? '-' }}</strong>
                            @if($jurnal->alokasiBopAktual->isNotEmpty())
                                <br><small class="text-success">Dialokasikan ke {{ $jurnal->alokasiBopAktual->count() }} job</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('jurnal-umum.show', $jurnal->id_jurnal) }}" class="fw-bold">
                                {{ $jurnal->nomor_bukti }}
                            </a>
                        </td>
                        <td>{{ $jurnal->keterangan }}</td>
                        <td class="text-end text-danger fw-bold">
                            Rp {{ number_format($jurnal->getTotalDebit(), 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <form action="{{ route('pengeluaran-bop.destroy', $jurnal->id_jurnal) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(this)" title="Batalkan Jurnal">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data pengeluaran BOP aktual.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $jurnals->links() }}
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Batalkan Jurnal ini?',
            text: "Data jurnal akan dihapus dan saldo akun akan dikembalikan seperti semula!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }
</script>
@endsection
