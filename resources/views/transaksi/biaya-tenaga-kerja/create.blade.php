@extends('layouts.app')

@section('title', 'Input Biaya Tenaga Kerja')
@section('page-title', 'Input Biaya Tenaga Kerja')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Input Biaya Tenaga Kerja</h4>
            <p class="text-muted mb-0">Input biaya tenaga kerja untuk job order</p>
        </div>
        <a href="{{ route('biaya-tenaga-kerja.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('biaya-tenaga-kerja.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kerja <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_kerja') is-invalid @enderror" 
                               name="tanggal_kerja" 
                               value="{{ old('tanggal_kerja', now()->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_kerja')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Job Order <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_permintaan_produksi') is-invalid @enderror" 
                                name="id_permintaan_produksi" 
                                id="jobSelect"
                                required>
                            <option value="">-- Pilih Job Order --</option>
                            @foreach($jobOrders as $job)
                                <option value="{{ $job->id_permintaan_produksi }}" 
                                        {{ old('id_permintaan_produksi', request('job_id')) == $job->id_permintaan_produksi ? 'selected' : '' }}>
                                    {{ $job->nomor_job }} - {{ $job->produk->nama_produk }}
                                    ({{ $job->status }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_permintaan_produksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tenaga Kerja <span class="text-danger">*</span></label>
                        <select class="form-select @error('id_tenaga') is-invalid @enderror" 
                                name="id_tenaga" 
                                id="tenagaSelect"
                                required>
                            <option value="">-- Pilih Tenaga Kerja --</option>
                            @foreach($tenagaKerja as $tenaga)
                                <option value="{{ $tenaga->id_tenaga }}" 
                                        data-upah="{{ $tenaga->upah_per_minggu }}"
                                        data-jabatan="{{ $tenaga->jabatan }}"
                                        {{ old('id_tenaga') == $tenaga->id_tenaga ? 'selected' : '' }}>
                                    {{ $tenaga->nama_tenaga }} - {{ $tenaga->jabatan }}
                                    (Rp {{ number_format($tenaga->upah_per_minggu, 0, ',', '.') }}/minggu)
                                </option>
                            @endforeach
                        </select>
                        @error('id_tenaga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Lama Pengerjaan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('hari_kerja') is-invalid @enderror" 
                                   name="hari_kerja" 
                                   id="hariKerja"
                                   value="{{ old('hari_kerja') }}"
                                   min="0.1"
                                   step="0.1"
                                   placeholder="Contoh: 1"
                                   required>
                            <span class="input-group-text">Hari</span>
                        </div>
                        <small class="text-muted">Lama pengerjaan job ini</small>
                        @error('hari_kerja')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Batch <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('jumlah_batch') is-invalid @enderror" 
                               name="jumlah_batch" 
                               id="jumlahBatch"
                               value="{{ old('jumlah_batch', 1) }}"
                               min="1"
                               step="1"
                               required>
                        <small class="text-muted">Jumlah batch</small>
                        @error('jumlah_batch')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Upah Per Minggu <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control @error('upah_per_minggu') is-invalid @enderror" 
                                   name="upah_per_minggu"
                                   id="upahPerMinggu"
                                   value="{{ old('upah_per_minggu') }}"
                                   min="0"
                                   step="1000"
                                   placeholder="Otomatis dari master"
                                   required>
                        </div>
                        <small class="text-muted">Default dari master, bisa diubah</small>
                        @error('upah_per_minggu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
            <!-- Tambahan untuk Absen dan Lembur -->
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Jam Absen</label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('jam_absen') is-invalid @enderror" 
                                   name="jam_absen" 
                                   id="jamAbsen"
                                   value="{{ old('jam_absen', 0) }}"
                                   min="0"
                                   step="0.5">
                            <span class="input-group-text">Jam</span>
                        </div>
                        <small class="text-muted">Lama tidak masuk</small>
                        @error('jam_absen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label text-danger">Nominal Potongan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control text-danger @error('nominal_potongan') is-invalid @enderror" 
                                   name="nominal_potongan"
                                   id="nominalPotongan"
                                   value="{{ old('nominal_potongan', 0) }}"
                                   min="0"
                                   step="500">
                        </div>
                        <small class="text-muted">Bisa diubah manual</small>
                        @error('nominal_potongan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label text-success">Nominal Lembur</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control text-success @error('nominal_lembur') is-invalid @enderror" 
                                   name="nominal_lembur"
                                   id="nominalLembur"
                                   value="{{ old('nominal_lembur', 0) }}"
                                   min="0"
                                   step="500">
                        </div>
                        <small class="text-muted">Tambahan uang lembur</small>
                        @error('nominal_lembur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Total Biaya Akhir</label>
                        <input type="text" 
                               class="form-control bg-light fw-bold text-primary fs-5" 
                               id="totalBiaya"
                               readonly
                               placeholder="Rp 0">
                        <small class="text-muted">(Upah Penuh) - Potongan + Lembur</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="2"
                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Cara Input Biaya Tenaga Kerja:</strong>
                <ol class="mb-0 mt-2">
                    <li>Pilih <strong>Tanggal Kerja</strong> (default hari ini)</li>
                    <li>Pilih <strong>Job Order</strong> yang sedang proses produksi</li>
                    <li>Pilih <strong>Tenaga Kerja</strong> — upah otomatis terisi dari master</li>
                    <li>Upah bisa <strong>diubah</strong> jika ada lembur/bonus</li>
                    <li>Input <strong>Lama Pengerjaan (Hari)</strong> dan <strong>Total Biaya</strong> dihitung otomatis</li>
                </ol>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Biaya Tenaga Kerja
                </button>
                <a href="{{ route('biaya-tenaga-kerja.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update upah ketika tenaga kerja dipilih
    document.getElementById('tenagaSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const upahDefault = parseFloat(selectedOption.getAttribute('data-upah')) || 0;
        
        // Set upah dari master (user bisa ubah manual)
        document.getElementById('upahPerMinggu').value = upahDefault;
        
        // Recalculate total
        calculateTotal();
    });
    
    // Auto-calculate suggested deduction when jam absen changes
    document.getElementById('jamAbsen').addEventListener('input', function() {
        const jamAbsen = parseFloat(this.value) || 0;
        const upahPerMinggu = parseFloat(document.getElementById('upahPerMinggu').value) || 0;
        
        // Asumsi standar 40 jam kerja per minggu untuk saran potongan
        if (jamAbsen > 0 && upahPerMinggu > 0) {
            const upahPerJam = upahPerMinggu / 40;
            const saranPotongan = Math.round(jamAbsen * upahPerJam);
            document.getElementById('nominalPotongan').value = saranPotongan;
        } else {
            document.getElementById('nominalPotongan').value = 0;
        }
        
        calculateTotal();
    });

    // Calculate total ketika input berubah
    document.getElementById('hariKerja').addEventListener('input', calculateTotal);
    document.getElementById('upahPerMinggu').addEventListener('input', function() {
        // Trigger jamAbsen recalculation if upah changes
        document.getElementById('jamAbsen').dispatchEvent(new Event('input'));
    });
    document.getElementById('nominalPotongan').addEventListener('input', calculateTotal);
    document.getElementById('nominalLembur').addEventListener('input', calculateTotal);
    
    function calculateTotal() {
        const hariKerja = parseFloat(document.getElementById('hariKerja').value) || 0;
        const upahPerMinggu = parseFloat(document.getElementById('upahPerMinggu').value) || 0;
        const nominalPotongan = parseFloat(document.getElementById('nominalPotongan').value) || 0;
        const nominalLembur = parseFloat(document.getElementById('nominalLembur').value) || 0;
        
        // Gaji pokok = (hari_kerja / 6 hari) * upah per minggu
        const porsiMinggu = hariKerja / 6;
        const upahPenuh = porsiMinggu * upahPerMinggu;
        let total = upahPenuh - nominalPotongan + nominalLembur;
        
        if(total < 0) total = 0; // Total tidak boleh negatif
        
        document.getElementById('totalBiaya').value = 
            'Rp ' + Math.round(total).toLocaleString('id-ID');
    }
    
    // Trigger on page load if old value exists
    if (document.getElementById('tenagaSelect').value) {
        // If no upah filled yet, get from selected tenaga
        if (!document.getElementById('upahPerMinggu').value) {
            document.getElementById('tenagaSelect').dispatchEvent(new Event('change'));
        }
        calculateTotal();
    }
</script>
@endsection