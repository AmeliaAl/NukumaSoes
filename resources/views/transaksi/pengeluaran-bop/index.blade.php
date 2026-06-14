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

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
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
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengeluaran BOP aktual.</td>
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
