@extends('layouts.app')

@section('title', 'Edit Biaya Overhead')
@section('page-title', 'Edit Biaya Overhead Pabrik')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Edit Biaya Overhead Pabrik</h4>
            <p class="text-muted mb-0">Update data biaya overhead pabrik</p>
        </div>
        <a href="{{ route('biaya-overhead-pabrik.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('biaya-overhead-pabrik.update', $overhead->id_biaya_overhead) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_overhead') is-invalid @enderror" 
                               name="tanggal_overhead" 
                               value="{{ old('tanggal_overhead', $overhead->tanggal_overhead->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_overhead')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Job Order</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $overhead->permintaanProduksi->nomor_job }} - {{ $overhead->permintaanProduksi->produk->nama_produk }}"
                               readonly>
                        <small class="text-muted">Job order tidak dapat diubah</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Jenis Overhead <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_kategori_bop') is-invalid @enderror" 
                                name="id_kategori_bop" 
                                id="jenisOverhead"
                                required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id_kategori_bop }}" 
                                        data-nama="{{ $cat->nama_kategori }}"
                                        {{ old('id_kategori_bop', $overhead->id_kategori_bop) == $cat->id_kategori_bop ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori_bop')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Satuan Periode <span class="text-danger">*</span></label>
                        <select class="form-select @error('satuan_periode') is-invalid @enderror" 
                                name="satuan_periode" 
                                id="satuanPeriode"
                                required>
                            <option value="per_batch" {{ old('satuan_periode', $overhead->satuan_periode) == 'per_batch' ? 'selected' : '' }}>Per Batch</option>
                            <option value="per_hari" {{ old('satuan_periode', $overhead->satuan_periode) == 'per_hari' ? 'selected' : '' }}>Per Hari</option>
                            <option value="per_minggu" {{ old('satuan_periode', $overhead->satuan_periode) == 'per_minggu' ? 'selected' : '' }}>Per Minggu</option>
                            <option value="per_bulan" {{ old('satuan_periode', $overhead->satuan_periode) == 'per_bulan' ? 'selected' : '' }}>Per Bulan</option>
                        </select>
                        @error('satuan_periode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Nominal <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('nominal') is-invalid @enderror" 
                                   name="nominal" 
                                   id="nominalOverhead"
                                   value="{{ old('nominal', $overhead->nominal) }}"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                        <small class="text-muted" id="nominalHelp">Nominal per batch</small>
                        @error('nominal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label" id="jumlahPeriodeLabel">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('jumlah_periode') is-invalid @enderror" 
                               name="jumlah_periode" 
                               id="jumlahPeriode"
                               value="{{ old('jumlah_periode', $overhead->jumlah_batch) }}"
                               min="1"
                               step="1"
                               required>
                        @error('jumlah_periode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Total Biaya</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" 
                                   class="form-control bg-light fw-bold text-primary" 
                                   id="totalBiaya"
                                   value="{{ number_format($overhead->total_biaya, 0, ',', '.') }}"
                                   readonly>
                        </div>
                        <small class="text-muted" id="totalHelp">Nominal × Jumlah</small>
                    </div>
                </div>
            </div>
            
            <!-- SEGMEN KALKULATOR TARIF BOP (Akan dimunculkan via JS jika jenisnya shared) -->
            <div class="card bg-light border-info mb-4 d-none" id="sharedBopSection">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0"><i class="fas fa-calculator me-2"></i>Kalkulator Tarif BOP</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-estimasi-tab" data-bs-toggle="pill" data-bs-target="#pills-estimasi" type="button" role="tab" aria-controls="pills-estimasi" aria-selected="true">Mode Estimasi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-aktual-tab" data-bs-toggle="pill" data-bs-target="#pills-aktual" type="button" role="tab" aria-controls="pills-aktual" aria-selected="false">Mode Aktual</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Mode Estimasi -->
                        <div class="tab-pane fade show active" id="pills-estimasi" role="tabpanel" aria-labelledby="pills-estimasi-tab">
                            <p class="small text-muted mb-3">Gunakan mode ini untuk menghitung tarif BOP Dibebankan berdasarkan estimasi pengeluaran <span class="jenis-overhead-text fw-bold text-info"></span> <span id="estimasiPeriodText">per bulan</span>.</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" id="estimasiBiayaLabel">Estimasi Biaya Sebulan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="estimasiBiaya" min="0" step="1000" placeholder="Contoh: 1000000">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" id="estimasiBatchLabel">Estimasi Total Produksi (Batch/Bulan)</label>
                                        <input type="number" class="form-control" id="estimasiBatch" value="100" min="1" step="1">
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="mb-3 w-100">
                                        <button type="button" class="btn btn-info w-100 text-white" onclick="hitungTarif('estimasi')">
                                            <i class="fas fa-check me-2"></i>Gunakan Tarif Estimasi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mode Aktual -->
                        <div class="tab-pane fade" id="pills-aktual" role="tabpanel" aria-labelledby="pills-aktual-tab">
                            <p class="small text-muted mb-3">Gunakan mode ini untuk membagi rata tagihan aktual <span class="jenis-overhead-text fw-bold text-info"></span> ke job order yang sedang aktif.</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Total Tagihan Aktual</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control @error('total_nominal_global') is-invalid @enderror" 
                                                   name="total_nominal_global" 
                                                   id="totalNominalGlobal"
                                                   value="{{ old('total_nominal_global', $overhead->total_nominal_global) }}"
                                                   min="0"
                                                   step="1000">
                                        </div>
                                        @error('total_nominal_global')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Total Batch Aktif (Semua Job)</label>
                                        <input type="number" 
                                               class="form-control @error('jumlah_batch_terlibat') is-invalid @enderror" 
                                               name="jumlah_batch_terlibat" 
                                               id="jumlahBatchTerlibat"
                                               value="{{ old('jumlah_batch_terlibat', $overhead->jumlah_batch_terlibat ?? $totalBatchAktif) }}"
                                               min="1"
                                               step="1">
                                        <small class="text-info" id="batchTerlibatHelp">Sistem mendeteksi ada {{ $totalBatchAktif }} batch aktif saat ini.</small>
                                        @error('jumlah_batch_terlibat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="mb-3 w-100">
                                        <button type="button" class="btn btn-info w-100 text-white" onclick="hitungTarif('aktual')">
                                            <i class="fas fa-check me-2"></i>Gunakan Tarif Aktual
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="2"
                          placeholder="Contoh: Gas LPG untuk oven produksi soes kering">{{ old('keterangan', $overhead->keterangan) }}</textarea>
                <small class="text-muted">Mesin/oven tidak dicatat sebagai BOP. Catat biaya operasionalnya, misalnya gas untuk oven tertentu.</small>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update
                </button>
                <a href="{{ route('biaya-overhead-pabrik.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const satuanLabels = {
        'per_batch': { label: 'Jumlah Batch', nominal: 'Nominal per batch', total: 'Nominal × Batch', bop: 'per batch', estimasiLabel: 'Estimasi Biaya Per Batch', batchLabel: 'Estimasi Total Produksi (Batch/Produksi)' },
        'per_hari':  { label: 'Jumlah Hari',  nominal: 'Nominal per hari',  total: 'Nominal × Hari',  bop: 'per hari',  estimasiLabel: 'Estimasi Biaya Sehari',   batchLabel: 'Estimasi Total Produksi (Batch/Hari)' },
        'per_minggu':{ label: 'Jumlah Minggu',nominal: 'Nominal per minggu',total: 'Nominal × Minggu',bop: 'per minggu',estimasiLabel: 'Estimasi Biaya Seminggu', batchLabel: 'Estimasi Total Produksi (Batch/Minggu)' },
        'per_bulan': { label: 'Jumlah Bulan', nominal: 'Nominal per bulan', total: 'Nominal × Bulan', bop: 'per bulan', estimasiLabel: 'Estimasi Biaya Sebulan',  batchLabel: 'Estimasi Total Produksi (Batch/Bulan)' }
    };

    // Cek jenis overhead untuk Shared BOP
    const sharedTypes = ['Listrik', 'Air', 'Gas', 'Asuransi Pabrik'];
    
    document.getElementById('jenisOverhead').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const jenis = selectedOption ? selectedOption.getAttribute('data-nama') : '';
        
        // Show/hide Shared BOP section
        if (sharedTypes.includes(jenis)) {
            document.getElementById('sharedBopSection').classList.remove('d-none');
            // Update teks span nama overhead
            document.querySelectorAll('.jenis-overhead-text').forEach(el => el.textContent = jenis);
        } else {
            document.getElementById('sharedBopSection').classList.add('d-none');
            // Clear shared fields
            document.getElementById('totalNominalGlobal').value = '';
        }
    });

    document.getElementById('satuanPeriode').addEventListener('change', updateLabels);

    function updateLabels() {
        const satuan = document.getElementById('satuanPeriode').value;
        const labels = satuanLabels[satuan] || satuanLabels['per_batch'];
        
        // Update field utama
        document.getElementById('jumlahPeriodeLabel').innerHTML = labels.label + ' <span class="text-danger">*</span>';
        document.getElementById('nominalHelp').textContent = labels.nominal;
        document.getElementById('totalHelp').textContent = labels.total;

        // Update label Kalkulator BOP
        const elPeriod = document.getElementById('estimasiPeriodText');
        const elBiaya  = document.getElementById('estimasiBiayaLabel');
        const elBatch  = document.getElementById('estimasiBatchLabel');
        if (elPeriod) elPeriod.textContent = labels.bop;
        if (elBiaya)  elBiaya.textContent  = labels.estimasiLabel;
        if (elBatch)  elBatch.textContent  = labels.batchLabel;
    }

    function calculateTotal() {
        const nominal = parseFloat(document.getElementById('nominalOverhead').value) || 0;
        const jumlah = parseFloat(document.getElementById('jumlahPeriode').value) || 1;
        const total = nominal * jumlah;
        document.getElementById('totalBiaya').value = total.toLocaleString('id-ID');
    }

    window.hitungTarif = function(mode) {
        let total = 0;
        let batch = 1;

        if (mode === 'estimasi') {
            total = parseFloat(document.getElementById('estimasiBiaya').value) || 0;
            batch = parseFloat(document.getElementById('estimasiBatch').value) || 1;
            
            // Clear field aktual agar tidak ikut tersubmit ke database
            document.getElementById('totalNominalGlobal').value = '';
            document.getElementById('jumlahBatchTerlibat').value = '';
        } else {
            total = parseFloat(document.getElementById('totalNominalGlobal').value) || 0;
            batch = parseFloat(document.getElementById('jumlahBatchTerlibat').value) || 1;
        }

        if (total > 0) {
            const satuan = document.getElementById('satuanPeriode').value;
            const nominalPerUnit = Math.round(total / batch);
            document.getElementById('nominalOverhead').value = nominalPerUnit;
            
            // Tidak reset satuan_periode — gunakan yang sudah dipilih user
            updateLabels();
            calculateTotal();
            
            const selectEl = document.getElementById('jenisOverhead');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const jenis = selectedOption ? selectedOption.getAttribute('data-nama') : 'BOP';
            const satuanInfo = satuanLabels[satuan] || satuanLabels['per_batch'];
            alert(`Tarif ${jenis} berhasil dihitung:\nRp ${nominalPerUnit.toLocaleString('id-ID')} ${satuanInfo.nominal}`);
        } else {
            alert('Silakan isi nilai biaya/tagihan terlebih dahulu!');
        }
    };

    document.getElementById('nominalOverhead').addEventListener('input', calculateTotal);
    document.getElementById('jumlahPeriode').addEventListener('input', calculateTotal);

    // Init on load
    updateLabels();
    document.getElementById('jenisOverhead').dispatchEvent(new Event('change'));
</script>
@endsection
