@extends('layouts.app')

@section('title', 'Master Data Akun')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Daftar Akun (Chart of Accounts)</h1>
            <p class="text-muted mb-0">Kelola master data akun untuk jurnal dan buku besar</p>
        </div>
        <div>
            <a href="{{ route('akun.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Akun
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Tipe Akun</th>
                            <th>Saldo Normal</th>
                            <th class="text-end">Saldo Saat Ini</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($akuns as $akun)
                        <tr>
                            <td><strong>{{ $akun->kode_akun }}</strong></td>
                            <td>{{ $akun->nama_akun }}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($akun->tipe_akun) }}</span></td>
                            <td><span class="badge {{ $akun->saldo_normal == 'debit' ? 'bg-primary' : 'bg-warning text-dark' }}">{{ ucfirst($akun->saldo_normal) }}</span></td>
                            <td class="text-end">
                                @php
                                    $saldoVal  = floatval($akun->saldo);
                                    $isAbnormal = ($akun->saldo_normal === 'debit'   && $saldoVal < 0)
                                               || ($akun->saldo_normal === 'kredit'  && $saldoVal < 0);
                                    $label = '';
                                    if ($saldoVal < 0) {
                                        $label = $akun->saldo_normal === 'debit' ? '(K)' : '(D)';
                                    }
                                @endphp
                                @if($isAbnormal)
                                    <span class="text-danger fw-semibold" title="Saldo tidak normal">
                                        Rp {{ number_format(abs($saldoVal), 0, ',', '.') }}
                                        <small class="badge bg-danger ms-1">{{ $label }} Abnormal</small>
                                    </span>
                                @else
                                    Rp {{ number_format(abs($saldoVal), 0, ',', '.') }}
                                @endif
                            </td>
                            <td>
                                @if($akun->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('akun.edit', $akun->id_akun) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('akun.destroy', $akun->id_akun) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data akun</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
