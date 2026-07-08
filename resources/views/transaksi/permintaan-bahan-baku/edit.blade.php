@extends('layouts.app')

@section('title', 'Edit Permintaan Bahan')
@section('page-title', 'Edit Permintaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Edit Permintaan Bahan Baku</h4>
            <p class="text-muted mb-0">Update data permintaan bahan baku</p>
        </div>
        <a href="{{ route('permintaan-bahan-baku.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('permintaan-bahan-baku.update', $permintaan->id_permintaan_bahan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nomor Permintaan</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $permintaan->nomor_permintaan }}"
                               readonly>
                        <small class="text-muted">Nomor tidak dapat diubah</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Permintaan <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_permintaan') is-invalid @enderror" 
                               name="tanggal_permintaan" 
                               value="{{ old('tanggal_permintaan', $permintaan->tanggal_permintaan->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_permintaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- TABEL BAHAN BAKU DINAMIS -->
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Bahan Baku</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="btnTambahBahan">
                        <i class="fas fa-plus me-1"></i> Tambah Bahan
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="tableBahan">
                            <thead class="table-light">
                                <tr>
                                    <th>Bahan Baku <span class="text-danger">*</span></th>
                                    <th width="200">Jumlah <span class="text-danger">*</span></th>
                                    <th width="80" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Baris akan ditambahkan via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    @error('bahan')
                        <div class="text-danger p-2"><small>{{ $message }}</small></div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="3">{{ old('keterangan', $permintaan->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            @if($permintaan->status_permintaan != 'pending')
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Permintaan ini sudah {{ $permintaan->status_permintaan == 'disetujui' ? 'disetujui' : 'ditolak' }}. 
                    Perubahan data mungkin mempengaruhi penerimaan bahan.
                </div>
            @endif
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update
                </button>
                <a href="{{ route('permintaan-bahan-baku.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bahanBakuData = {!! $bahanBakuJson !!};
        const existingDetails = {!! json_encode($permintaan->details->map(function($d) {
            $isi = floatval($d->bahanBaku->isi_per_kemasan ?? 1);
            $isi = $isi > 0 ? $isi : 1;
            $jumlahBeli = $d->jumlah_permintaan / $isi;
            return [
                'id_bahan' => $d->id_bahan,
                'jumlah' => $jumlahBeli,
                'satuan' => $d->bahanBaku->satuan,
                'satuan_beli' => $d->bahanBaku->satuan_beli,
                'isi_per_kemasan' => $isi
            ];
        })) !!};
        
        const tableBody = document.querySelector('#tableBahan tbody');
        const btnTambah = document.getElementById('btnTambahBahan');
        let rowCount = 0;

        function addRow(idBahan = '', jumlah = '', satuanPakai = 'Unit', satuanBeli = '', isiPerKemasan = 1) {
            const tr = document.createElement('tr');
            
            let currentSatuanBeli = satuanBeli || satuanPakai;
            
            let optionsHtml = '<option value="">-- Pilih Bahan Baku --</option>';
            bahanBakuData.forEach(bahan => {
                const isSelected = (bahan.id == idBahan) ? 'selected' : '';
                const satuanBeliVal = bahan.satuan_beli || bahan.satuan;
                const isiPerKemasanVal = bahan.isi_per_kemasan || 1;
                optionsHtml += `<option value="${bahan.id}" data-satuan="${bahan.satuan}" data-satuan-beli="${satuanBeliVal}" data-isi="${isiPerKemasanVal}" ${isSelected}>${bahan.nama} (${bahan.kode})</option>`;
            });

            tr.innerHTML = `
                <td>
                    <select name="bahan[${rowCount}][id_bahan]" class="form-select select-bahan" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="bahan[${rowCount}][jumlah]" class="form-control input-jumlah" value="${jumlah}" step="0.01" min="0.01" required>
                        <span class="input-group-text satuan-label">${currentSatuanBeli}</span>
                    </div>
                    <br><small class="text-muted convert-hint"></small>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tableBody.appendChild(tr);
            
            // Event listener untuk ubah satuan
            const selectElement = tr.querySelector('.select-bahan');
            const satuanLabel = tr.querySelector('.satuan-label');
            const inputJumlah = tr.querySelector('.input-jumlah');
            const hintElement = tr.querySelector('.convert-hint');
            
            let currentSatuanPakai = satuanPakai;
            let currentIsi = isiPerKemasan;
            
            selectElement.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if(selectedOption.value) {
                    const newSatuanBeli = selectedOption.getAttribute('data-satuan-beli') || selectedOption.getAttribute('data-satuan');
                    satuanLabel.textContent = newSatuanBeli || 'Unit';
                    
                    currentSatuanPakai = selectedOption.getAttribute('data-satuan');
                    currentIsi = parseFloat(selectedOption.getAttribute('data-isi')) || 1;
                    updateHint();
                }
            });
            
            const updateHint = () => {
                const jml = parseFloat(inputJumlah.value) || 0;
                const totalPakai = jml * currentIsi;
                if(hintElement) {
                    hintElement.innerHTML = `Setara dengan: <strong>${totalPakai} ${currentSatuanPakai}</strong>`;
                }
            };

            inputJumlah.addEventListener('input', updateHint);
            
            // Trigger update hint onload
            if (jumlah !== '') updateHint();
            
            // Event listener hapus
            tr.querySelector('.btn-hapus').addEventListener('click', function() {
                tr.remove();
            });

            rowCount++;
        }

        // Load existing details
        if (existingDetails.length > 0) {
            existingDetails.forEach(detail => {
                addRow(detail.id_bahan, detail.jumlah, detail.satuan, detail.satuan_beli, detail.isi_per_kemasan);
            });
        } else {
            addRow(); // fallback if empty
        }

        btnTambah.addEventListener('click', () => addRow());
    });
</script>
@endsection