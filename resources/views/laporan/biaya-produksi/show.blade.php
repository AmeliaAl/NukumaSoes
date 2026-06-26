@extends('layouts.app')

@section('title', 'Detail Laporan Biaya Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">📋 Detail Laporan Biaya Produksi</h1>
            <p class="text-muted mb-0">{{ $jobOrder->nomor_job }}</p>
        </div>
        <div>
            <a href="{{ route('laporan.biaya-produksi.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('laporan.biaya-produksi.kartu-biaya', $jobOrder->id_permintaan_produksi) }}" 
               class="btn btn-primary">
                <i class="fas fa-file-invoice me-1"></i> Kartu Biaya
            </a>
            <a href="{{ route('laporan.biaya-produksi.pdf', $jobOrder->id_permintaan_produksi) }}" 
               class="btn btn-danger" 
               target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Job Order Info -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Job Order</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>No. Job Order</strong></td>
                                    <td>: {{ $jobOrder->nomor_job }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Produk</strong></td>
                                    <td>: {{ $jobOrder->produk->nama_produk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah Produksi</strong></td>
                                    <td>: {{ number_format($jobOrder->jumlah_produksi, 0, ',', '.') }} {{ $jobOrder->produk->satuan_produk ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah Batch</strong></td>
                                    <td>: {{ $jobOrder->jumlah_batch ?? 1 }} Batch</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Produksi</strong></td>
                                    <td>: {{ $jobOrder->jenis_produksi == 'maklun' ? 'Maklun ('.($jobOrder->nama_customer_maklun ?? '-').')' : 'Brand Sendiri' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: 
                                        @if($jobOrder->status == 'selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($jobOrder->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Tanggal Mulai</strong></td>
                                    <td>: {{ \Carbon\Carbon::parse($jobOrder->tanggal_mulai)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Selesai</strong></td>
                                    <td>: {{ $jobOrder->tanggal_selesai ? \Carbon\Carbon::parse($jobOrder->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tahap Produksi</strong></td>
                                    <td>: {{ ucfirst($jobOrder->tahap_produksi ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tujuan Produksi</strong></td>
                                    <td>: {{ ucwords(str_replace('_', ' ', $jobOrder->tujuan_produksi ?? '-')) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Keterangan</strong></td>
                                    <td>: {{ $jobOrder->keterangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Biaya -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Ringkasan Biaya</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td>Biaya Bahan Baku</td>
                            <td class="text-end">Rp {{ number_format($jobOrder->total_biaya_bahan ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Biaya Tenaga Kerja</td>
                            <td class="text-end">Rp {{ number_format($jobOrder->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Biaya Overhead</td>
                            <td class="text-end">Rp {{ number_format($jobOrder->total_biaya_overhead ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>TOTAL BIAYA</strong></td>
                            <td class="text-end">
                                <strong class="text-success">Rp {{ number_format($jobOrder->total_biaya_produksi ?? 0, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>HPP per Unit</strong></td>
                            <td class="text-end">
                                <span class="badge bg-success fs-6">
                                    Rp {{ number_format($jobOrder->harga_pokok_per_unit ?? 0, 0, ',', '.') }} / {{ $jobOrder->produk->satuan_produk ?? 'Unit' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Biaya Bahan Baku -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-box text-primary me-2"></i>Detail Biaya Bahan Baku</h5>
        </div>
        <div class="card-body">
            @if($jobOrder->pemakaianBahanBakuLangsung->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Bahan Baku</th>
                                <th>Satuan</th>
                                <th class="text-end">Jumlah Pakai</th>
                                <th class="text-end">Harga/Satuan (FIFO)</th>
                                <th class="text-end">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobOrder->pemakaianBahanBakuLangsung as $index => $pemakaian)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pemakaian->bahanBaku->nama_bahan ?? '-' }}</td>
                                <td>{{ $pemakaian->bahanBaku->satuan ?? '-' }}</td>
                                <td class="text-end">{{ number_format($pemakaian->jumlah_pakai, 2, ',', '.') }}</td>
                                <td class="text-end">
                                    <strong class="text-info">Rp {{ number_format($pemakaian->harga_satuan ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">Rp {{ number_format($pemakaian->total_biaya ?? 0, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">TOTAL BIAYA BAHAN BAKU:</th>
                                <th class="text-end">Rp {{ number_format($jobOrder->total_biaya_bahan ?? 0, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>Belum ada data pemakaian bahan baku</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Biaya Tenaga Kerja - FIXED DENGAN JENIS DARI DATABASE -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-users text-success me-2"></i>Detail Biaya Tenaga Kerja</h5>
        </div>
        <div class="card-body">
            @if($jobOrder->biayaTenagaKerjaLangsung->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Tenaga Kerja</th>
                                <th>Jenis</th> <!-- ← KOLOM INI SEKARANG TERISI -->
                                <th class="text-end">Jam Kerja</th>
                                <th class="text-end">Batch</th>
                                <th class="text-end">Upah/Jam</th>
                                <th class="text-end">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobOrder->biayaTenagaKerjaLangsung as $index => $biaya)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $biaya->tenagaKerja->nama_tenaga ?? '-' }}</td>
                                
                                <!-- ========== FIXED: AMBIL DARI FIELD jenis_tenaga ========== -->
                                <td>
                                    @if($biaya->tenagaKerja)
                                        @if($biaya->tenagaKerja->jenis_tenaga == 'langsung')
                                            <span class="badge bg-primary">Langsung</span>
                                        @elseif($biaya->tenagaKerja->jenis_tenaga == 'tidak_langsung')
                                            <span class="badge bg-secondary text-white">Tidak Langsung</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum diset</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <!-- ======================================================= -->
                                
                                <td class="text-end">{{ number_format($biaya->jam_kerja ?? 0, 1, ',', '.') }} jam</td>
                                <td class="text-end">{{ $biaya->jumlah_batch ?? 1 }}</td>
                                <td class="text-end">Rp {{ number_format($biaya->upah_per_jam ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <strong class="text-primary">Rp {{ number_format($biaya->total_biaya ?? 0, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="6" class="text-end">TOTAL BIAYA TENAGA KERJA:</th>
                                <th class="text-end">Rp {{ number_format($jobOrder->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>Belum ada data biaya tenaga kerja</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Biaya Overhead Pabrik -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-cog text-warning me-2"></i>Detail Biaya Overhead Pabrik</h5>
        </div>
        <div class="card-body">
            @php
                $allBop = collect();
                foreach($jobOrder->biayaOverheadPabrik as $b) {
                    $allBop->push([
                        'jenis' => $b->jenis_overhead ?? $b->jenis_biaya ?? 'Overhead Pabrik',
                        'keterangan' => $b->keterangan,
                        'batch' => $b->jumlah_batch ?? 1,
                        'biaya' => $b->total_biaya ?? ($b->nominal ?? 0)
                    ]);
                }
                foreach($jobOrder->biayaTenagaKerjaTidakLangsung as $tk) {
                    $allBop->push([
                        'jenis' => 'BTK Tidak Langsung (BTKTL)',
                        'keterangan' => 'Upah ' . ($tk->tenagaKerja->nama_tenaga ?? '') . ' (' . ($tk->tenagaKerja->jabatan ?? '') . ')',
                        'batch' => $tk->jumlah_batch ?? 1,
                        'biaya' => $tk->total_biaya
                    ]);
                }
                foreach($jobOrder->pemakaianBahanBakuTidakLangsung as $bh) {
                    $allBop->push([
                        'jenis' => 'Bahan Penolong / BOP',
                        'keterangan' => 'Pemakaian ' . ($bh->bahanBaku->nama_bahan ?? '') . ' (' . number_format($bh->jumlah_pakai, 2) . ' ' . ($bh->bahanBaku->satuan ?? '') . ')',
                        'batch' => $jobOrder->jumlah_batch ?? 1,
                        'biaya' => $bh->total_biaya
                    ]);
                }
            @endphp

            @if($allBop->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Jenis Biaya</th>
                                <th>Keterangan</th>
                                <th class="text-end">Batch</th>
                                <th class="text-end">Jumlah Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allBop as $index => $overhead)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @php
                                        $jenisOverhead = $overhead['jenis'];
                                        $badgeColor = 'secondary';
                                        
                                        if (in_array($jenisOverhead, ['Listrik', 'Air', 'Gas'])) {
                                            $badgeColor = 'primary';
                                        } elseif (in_array($jenisOverhead, ['Asuransi', 'Bahan Penolong'])) {
                                            $badgeColor = 'info';
                                        } elseif ($jenisOverhead == 'BTK Tidak Langsung (BTKTL)') {
                                            $badgeColor = 'dark';
                                        } elseif ($jenisOverhead == 'Bahan Penolong / BOP') {
                                            $badgeColor = 'success';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}">{{ $jenisOverhead }}</span>
                                </td>
                                <td>{{ $overhead['keterangan'] ?? '-' }}</td>
                                <td class="text-end">{{ $overhead['batch'] ?? 1 }}</td>
                                <td class="text-end">
                                    <strong class="text-primary">Rp {{ number_format($overhead['biaya'], 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">TOTAL BIAYA OVERHEAD:</th>
                                <th class="text-end">Rp {{ number_format($jobOrder->total_biaya_overhead ?? 0, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>Belum ada data biaya overhead pabrik</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-borderless td {
        padding: 0.5rem 0.75rem;
    }
    
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
    
    /* Badge color consistency */
    .badge.bg-primary {
        background-color: #0d6efd !important;
    }
    
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #000 !important;
    }
</style>
@endpush
@endsection
