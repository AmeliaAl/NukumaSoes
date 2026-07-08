@extends('layouts.app')

@section('title', 'Edit Biaya Tenaga Kerja')
@section('page-title', 'Edit Biaya Tenaga Kerja')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Edit Biaya Tenaga Kerja</h4>
            <p class="text-muted mb-0">Update data biaya tenaga kerja</p>
        </div>
        <a href="{{ route('biaya-tenaga-kerja.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('biaya-tenaga-kerja.update', $biaya->id_biaya_tenaga_kerja) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kerja <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_kerja') is-invalid @enderror" 
                               name="tanggal_kerja" 
                               value="{{ old('tanggal_kerja', $biaya->tanggal_kerja->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_kerja')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Job Order</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $biaya->permintaanProduksi->nomor_job }} - {{ $biaya->permintaanProduksi->produk->nama_produk }}"
                               readonly>
                        <small class="text-muted">Job order tidak dapat diubah</small>
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
                                        {{ old('id_tenaga', $biaya->id_tenaga) == $tenaga->id_tenaga ? 'selected' : '' }}>
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
                                   value="{{ old('hari_kerja', $biaya->hari_kerja) }}"
                                   min="0.1"
                                   step="0.1"
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
                               value="{{ old('jumlah_batch', $biaya->jumlah_batch) }}"
                               min="1"
                               step="1"
                               required>
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
                                   value="{{ old('upah_per_minggu', $biaya->upah_per_minggu) }}"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                        <small class="text-muted">Bisa diubah dari default master</small>
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
                                   value="{{ old('jam_absen', $biaya->jam_absen) }}"
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
                                   value="{{ old('nominal_potongan', $biaya->nominal_potongan) }}"
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
                                   value="{{ old('nominal_lembur', $biaya->nominal_lembur) }}"
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
                               value="Rp {{ number_format($biaya->total_biaya, 0, ',', '.') }}"
                               readonly>
                        <small class="text-muted">(Upah Penuh) - Potongan + Lembur</small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update
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
    document.getElementById('tenagaSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const upahDefault = parseFloat(selectedOption.getAttribute('data-upah')) || 0;
        document.getElementById('upahPerMinggu').value = upahDefault;
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
    
    // Initial calculation
    calculateTotal();
</script>
@endsection