@extends('layouts.app')

@section('title', 'Detail Job Order')
@section('page-title', 'Detail Job Order')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Detail Job Order: {{ $job->nomor_job }}</h4>
            <p class="text-muted mb-0">Rincian biaya produksi dengan metode Job Order Costing</p>
        </div>
        <div class="d-flex gap-2">
            @if($job->status != 'selesai')
                <a href="{{ route('permintaan-produksi.edit', $job->id_permintaan_produksi) }}" 
                   class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            @endif
            
            @if($job->status == 'selesai')
                <a href="{{ route('laporan.biaya-produksi.show', $job->id_permintaan_produksi) }}" 
                   class="btn btn-success">
                    <i class="fas fa-file-invoice me-2"></i>Lihat Laporan
                </a>
                <a href="{{ route('laporan.biaya-produksi.pdf', $job->id_permintaan_produksi) }}" 
                   class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
            @endif
            
            <a href="{{ route('permintaan-produksi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

{{-- CRITICAL FIX: Show success messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        
        @if(session('biaya_breakdown'))
            <div class="mt-2 small">
                <strong>Breakdown Biaya:</strong>
                <ul class="mb-0">
                    <li>Bahan Baku: Rp {{ number_format(session('biaya_breakdown')['bahan_baku'], 0, ',', '.') }}</li>
                    <li>Tenaga Kerja: Rp {{ number_format(session('biaya_breakdown')['tenaga_kerja'], 0, ',', '.') }}</li>
                    <li>Overhead: Rp {{ number_format(session('biaya_breakdown')['overhead'], 0, ',', '.') }}</li>
                    <li><strong>Total: Rp {{ number_format(session('biaya_breakdown')['total'], 0, ',', '.') }}</strong></li>
                </ul>
            </div>
        @endif
        
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>
        <strong>Periksa input:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Info Job Order -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Job Order</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%">Nomor Job</th>
                        <td><strong>{{ $job->nomor_job }}</strong></td>
                    </tr>
                    <tr>
                        <th>Produk</th>
                        <td><strong>{{ $job->produk->nama_produk }}</strong></td>
                    </tr>
                    <tr>
                        <th>Jumlah Produksi</th>
                        <td><strong>{{ number_format($job->jumlah_produksi, 0) }} {{ $job->produk->satuan_produk }}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Batch Direncanakan</th>
                        <td><strong>{{ $job->jumlah_batch ?? 1 }} Batch</strong></td>
                    </tr>
                    <tr>
                        <th>Jenis Produksi</th>
                        <td>
                            @if($job->jenis_produksi == 'maklun')
                                Maklun ({{ $job->nama_customer_maklun ?? '-' }})
                            @else
                                Brand Sendiri
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tujuan Produksi</th>
                        <td>{{ ucwords(str_replace('_', ' ', $job->tujuan_produksi)) }}</td>
                    </tr>
                    <tr>
                        <th>Tahap Produksi</th>
                        <td>{{ ucfirst($job->tahap_produksi) }}</td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td>{{ $job->customer ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Mulai</th>
                        <td>{{ $job->tanggal_mulai->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Selesai</th>
                        <td>
                            @if($job->tanggal_selesai)
                                {{ $job->tanggal_selesai->format('d F Y') }}
                            @else
                                <span class="text-muted">Belum selesai</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($job->status == 'pending')
                                <span class="badge bg-secondary">Pending</span>
                            @elseif($job->status == 'proses')
                                <span class="badge bg-primary">Proses</span>
                            @else
                                <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $job->keterangan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Ringkasan Biaya Produksi</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="50%">Biaya Bahan Baku</th>
                        <td class="text-end">
                            <strong>Rp {{ number_format($job->total_biaya_bahan ?? 0, 0, ',', '.') }}</strong>
                            {{-- CRITICAL FIX: Debug indicator --}}
                            @php
                                $manualSumBahan = $job->pemakaianBahanBaku->sum('total_biaya');
                            @endphp
                            @if(($job->total_biaya_bahan ?? 0) == 0 && $manualSumBahan > 0)
                                <br><small class="text-danger">⚠️ Tidak sinkron (Manual: Rp {{ number_format($manualSumBahan, 0, ',', '.') }})</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Biaya Tenaga Kerja</th>
                        <td class="text-end"><strong>Rp {{ number_format($job->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Biaya Overhead Pabrik</th>
                        <td class="text-end"><strong>Rp {{ number_format($job->total_biaya_overhead ?? 0, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr class="border-top">
                        <th>Total Biaya Produksi</th>
                        <td class="text-end">
                            <h4 class="text-primary mb-0">Rp {{ number_format($job->total_biaya_produksi ?? 0, 0, ',', '.') }}</h4>
                        </td>
                    </tr>
                    <tr class="border-top">
                        <th>Biaya Per Unit</th>
                        <td class="text-end">
                            <h5 class="text-success mb-0">
                                Rp {{ number_format($job->harga_pokok_per_unit ?? 0, 0, ',', '.') }} / {{ $job->produk->satuan_produk }}
                            </h5>
                        </td>
                    </tr>
                </table>
                
                @if($job->status != 'selesai')
                    <div class="alert alert-warning mb-0 mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small><strong>Job Order masih dalam proses.</strong> Biaya akan dikalkulasi ulang setiap ada transaksi baru.</small>
                    </div>
                    
                    {{-- CRITICAL FIX: Action buttons --}}
                    <div class="d-flex gap-2 mt-3">
                        <form action="{{ route('permintaan-produksi.recalculate', $job->id_permintaan_produksi) }}" 
                              method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-info w-100 btn-sm" title="Hitung ulang semua biaya">
                                <i class="fas fa-sync-alt me-2"></i>Hitung Ulang Biaya
                            </button>
                        </form>
                        
                        <form action="{{ route('permintaan-produksi.complete', $job->id_permintaan_produksi) }}" 
                              method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('Selesaikan job order ini? Biaya akan dikalkulasi final dan status tidak bisa diubah lagi!')">
                                <i class="fas fa-check-circle me-2"></i>Selesaikan Job Order
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@php
    $targetProduksi = (float) $job->jumlah_produksi;
    $totalTargetBatch = (float) $job->batchProduksi->where('status', '!=', 'dibatalkan')->sum('jumlah_target');
    $totalHasilBatch = (float) $job->batchProduksi->where('status', '!=', 'dibatalkan')->sum('jumlah_hasil');
    $sisaProduksi = max(0, $targetProduksi - $totalHasilBatch);
    $batchAktif = $job->batchProduksi->whereIn('status', ['proses', 'selesai'])->count();
    $batchBatal = $job->batchProduksi->where('status', 'dibatalkan')->count();
@endphp

<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Pelaksanaan Batch Produksi</h5>
        <span>{{ $job->batchProduksi->where('status', 'selesai')->count() }} selesai, {{ $batchBatal }} batal</span>
    </div>
    <div class="card-body">
        <p class="text-muted small">
            Satu Job Order dapat diselesaikan melalui beberapa batch. Tanggal setiap batch menentukan apakah batch tersebut menerima alokasi BOP aktual pada suatu periode.
        </p>
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Target Job Order</small>
                    <strong>{{ number_format($targetProduksi, 2, ',', '.') }} {{ $job->produk->satuan_produk }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Target Batch Aktif</small>
                    <strong>{{ number_format($totalTargetBatch, 2, ',', '.') }} {{ $job->produk->satuan_produk }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Hasil Aktual Batch</small>
                    <strong>{{ number_format($totalHasilBatch, 2, ',', '.') }} {{ $job->produk->satuan_produk }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Sisa Produksi</small>
                    <strong class="{{ $sisaProduksi > 0 ? 'text-warning' : 'text-success' }}">{{ number_format($sisaProduksi, 2, ',', '.') }} {{ $job->produk->satuan_produk }}</strong>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Batch</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th class="text-end">Target</th>
                        <th class="text-end">Hasil Aktual</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        @if($job->status !== 'selesai')<th>Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($job->batchProduksi as $batch)
                        @if($job->status !== 'selesai')
                            <form id="batch-form-{{ $batch->id_batch_produksi }}" method="POST"
                                  action="{{ route('permintaan-produksi.batch.update', [$job->id_permintaan_produksi, $batch->id_batch_produksi]) }}">
                                @csrf
                                @method('PUT')
                            </form>
                        @endif
                        <tr>
                            <td>
                                <strong>Batch {{ $batch->urutan }}</strong>
                                <br>
                                <span class="badge {{ $batch->jenis_batch === 'tambahan' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $batch->jenis_batch === 'tambahan' ? 'Tambahan' : 'Rencana' }}
                                </span>
                            </td>
                            <td><input form="batch-form-{{ $batch->id_batch_produksi }}" type="date" name="tanggal_mulai" class="form-control form-control-sm" value="{{ $batch->tanggal_mulai?->format('Y-m-d') }}" required {{ $job->status === 'selesai' ? 'disabled' : '' }}></td>
                            <td><input form="batch-form-{{ $batch->id_batch_produksi }}" type="date" name="tanggal_selesai" class="form-control form-control-sm" value="{{ $batch->tanggal_selesai?->format('Y-m-d') }}" {{ $job->status === 'selesai' ? 'disabled' : '' }}></td>
                            <td><input form="batch-form-{{ $batch->id_batch_produksi }}" type="number" min="0" step="0.01" name="jumlah_target" class="form-control form-control-sm text-end" value="{{ $batch->jumlah_target }}" required {{ $job->status === 'selesai' ? 'disabled' : '' }}></td>
                            <td><input form="batch-form-{{ $batch->id_batch_produksi }}" type="number" min="0" step="0.01" name="jumlah_hasil" class="form-control form-control-sm text-end" value="{{ $batch->jumlah_hasil }}" required {{ $job->status === 'selesai' ? 'disabled' : '' }}></td>
                            <td>
                                <select form="batch-form-{{ $batch->id_batch_produksi }}" name="status" class="form-select form-select-sm" required {{ $job->status === 'selesai' ? 'disabled' : '' }}>
                                    <option value="rencana" @selected($batch->status === 'rencana')>Rencana</option>
                                    <option value="proses" @selected($batch->status === 'proses')>Proses</option>
                                    <option value="selesai" @selected($batch->status === 'selesai')>Selesai</option>
                                    <option value="dibatalkan" @selected($batch->status === 'dibatalkan')>Dibatalkan</option>
                                </select>
                            </td>
                            <td><input form="batch-form-{{ $batch->id_batch_produksi }}" type="text" maxlength="255" name="keterangan" class="form-control form-control-sm" value="{{ $batch->keterangan }}" {{ $job->status === 'selesai' ? 'disabled' : '' }}></td>
                            @if($job->status !== 'selesai')
                                <td><button form="batch-form-{{ $batch->id_batch_produksi }}" class="btn btn-primary btn-sm">Simpan</button></td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">Belum ada rincian batch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($job->status !== 'selesai')
            <div class="border rounded p-3 mt-3">
                <h6 class="mb-2">Tambah Batch Tambahan</h6>
                <form action="{{ route('permintaan-produksi.batch.store', $job->id_permintaan_produksi) }}" method="POST" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label small">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Target Batch</label>
                        <input type="number" name="jumlah_target" class="form-control form-control-sm" min="0.01" step="0.01" value="{{ number_format($sisaProduksi, 2, '.', '') }}" {{ $sisaProduksi <= 0 ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control form-control-sm" maxlength="255" placeholder="Opsional" {{ $sisaProduksi <= 0 ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary btn-sm w-100" {{ $sisaProduksi <= 0 ? 'disabled' : '' }}>
                            Tambah Batch
                        </button>
                    </div>
                </form>
                <small class="text-muted">Gunakan ini jika realisasi batch rencana belum memenuhi target produksi.</small>
            </div>
        @endif
    </div>
</div>

<!-- Detail Biaya Bahan Baku -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Detail Biaya Bahan Baku (FIFO Method)</h5>
    </div>
    <div class="card-body">
        @if($job->pemakaianBahanBakuLangsung->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Bahan Baku</th>
                            <th class="text-end">Jumlah Pakai</th>
                            <th class="text-end">Harga/Satuan</th>
                            <th class="text-end">Total Biaya</th>
                            <th>Batch FIFO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($job->pemakaianBahanBakuLangsung as $pakai)
                        <tr>
                            <td>{{ $pakai->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $pakai->bahanBaku->nama_bahan ?? '-' }}</strong>
                                <br>
                                <small class="text-muted">{{ $pakai->bahanBaku->kode_bahan ?? '-' }}</small>
                            </td>
                            <td class="text-end">{{ number_format($pakai->jumlah_pakai, 2) }} {{ $pakai->bahanBaku->satuan ?? '-' }}</td>
                            <td class="text-end">Rp {{ number_format($pakai->harga_satuan ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($pakai->total_biaya, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($pakai->stokBahanBaku)
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $pakai->stokBahanBaku->tanggal_masuk->format('d/m/Y') }}
                                    </small>
                                    <br>
                                    <span class="badge bg-secondary">#{{ $pakai->id_stok }}</span>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total Biaya Bahan Baku:</th>
                            <th class="text-end" colspan="2">
                                Rp {{ number_format($job->total_biaya_bahan ?? 0, 0, ',', '.') }}
                                {{-- CRITICAL FIX: Verification --}}
                                @php
                                    $manualSum = $job->pemakaianBahanBakuLangsung->sum('total_biaya');
                                @endphp
                                @if($manualSum != ($job->total_biaya_bahan ?? 0))
                                    <br><small class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Sum Manual: Rp {{ number_format($manualSum, 0, ',', '.') }}
                                        @if($job->status != 'selesai')
                                            <form action="{{ route('permintaan-produksi.recalculate', $job->id_permintaan_produksi) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning ms-2">
                                                    <i class="fas fa-sync-alt me-1"></i>Fix
                                                </button>
                                            </form>
                                        @endif
                                    </small>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Belum ada pemakaian bahan baku</p>
            </div>
        @endif
    </div>
</div>

<!-- Detail Biaya Tenaga Kerja -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Detail Biaya Tenaga Kerja</h5>
    </div>
    <div class="card-body">
        @slot('biayaTenagaKerjaLangsungCount', $job->biayaTenagaKerjaLangsung->count())
        @if($job->biayaTenagaKerjaLangsung->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Tenaga Kerja</th>
                            <th class="text-end">Jam Kerja</th>
                            <th class="text-end">Batch</th>
                            <th class="text-end">Upah/Jam</th>
                            <th class="text-end">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($job->biayaTenagaKerjaLangsung as $biaya)
                        <tr>
                            <td>{{ $biaya->tanggal_kerja ? $biaya->tanggal_kerja->format('d/m/Y') : $biaya->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $biaya->tenagaKerja->nama_tenaga }}</strong>
                                <br>
                                <small class="text-muted">{{ $biaya->tenagaKerja->jabatan }}</small>
                            </td>
                            <td class="text-end">{{ number_format($biaya->jam_kerja, 1) }} jam</td>
                            <td class="text-end">{{ $biaya->jumlah_batch ?? 1 }}</td>
                            <td class="text-end">Rp {{ number_format($biaya->upah_per_jam, 0, ',', '.') }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($biaya->total_biaya, 0, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Total Biaya Tenaga Kerja:</th>
                            <th class="text-end">Rp {{ number_format($job->total_biaya_tenaga_kerja ?? 0, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Belum ada biaya tenaga kerja</p>
            </div>
        @endif
    </div>
</div>

<!-- Detail Biaya Overhead -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-industry me-2"></i>Detail Biaya Overhead Pabrik</h5>
    </div>
    <div class="card-body">
        @php
            $allBop = collect();
            foreach($job->biayaOverheadPabrik as $b) {
                $allBop->push([
                    'tanggal' => $b->created_at->format('d/m/Y H:i'),
                    'jenis' => $b->jenis_overhead ?? $b->jenis_biaya ?? 'Overhead Pabrik',
                    'keterangan' => $b->keterangan,
                    'batch' => $b->jumlah_batch ?? 1,
                    'biaya' => $b->total_biaya ?? ($b->nominal ?? 0)
                ]);
            }
            foreach($job->biayaTenagaKerjaTidakLangsung as $tk) {
                $allBop->push([
                    'tanggal' => $tk->tanggal_kerja ? $tk->tanggal_kerja->format('d/m/Y') : $tk->created_at->format('d/m/Y H:i'),
                    'jenis' => 'BTK Tidak Langsung (BTKTL)',
                    'keterangan' => 'Upah ' . ($tk->tenagaKerja->nama_tenaga ?? '') . ' (' . ($tk->tenagaKerja->jabatan ?? '') . ')',
                    'batch' => $tk->jumlah_batch ?? 1,
                    'biaya' => $tk->total_biaya
                ]);
            }
            foreach($job->pemakaianBahanBakuTidakLangsung as $bh) {
                $allBop->push([
                    'tanggal' => $bh->created_at->format('d/m/Y H:i'),
                    'jenis' => 'Bahan Penolong / BOP',
                    'keterangan' => 'Pemakaian ' . ($bh->bahanBaku->nama_bahan ?? '') . ' (' . number_format($bh->jumlah_pakai, 2) . ' ' . ($bh->bahanBaku->satuan ?? '') . ')',
                    'batch' => $job->jumlah_batch ?? 1,
                    'biaya' => $bh->total_biaya
                ]);
            }
        @endphp

        @if($allBop->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Overhead</th>
                            <th>Keterangan</th>
                            <th class="text-end">Batch</th>
                            <th class="text-end">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allBop as $overhead)
                        <tr>
                            <td>{{ $overhead['tanggal'] }}</td>
                            <td><strong>{{ $overhead['jenis'] }}</strong></td>
                            <td>{{ $overhead['keterangan'] ?? '-' }}</td>
                            <td class="text-end">{{ $overhead['batch'] ?? 1 }}</td>
                            <td class="text-end"><strong>Rp {{ number_format($overhead['biaya'], 0, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total Biaya Overhead:</th>
                            <th class="text-end">Rp {{ number_format($job->total_biaya_overhead ?? 0, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Belum ada biaya overhead</p>
            </div>
        @endif
    </div>
</div>

@endsection
