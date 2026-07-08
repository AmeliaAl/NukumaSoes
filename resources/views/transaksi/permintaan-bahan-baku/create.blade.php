@extends('layouts.app')

@section('title', 'Buat Permintaan Bahan')
@section('page-title', 'Buat Permintaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Buat Permintaan Bahan Baku</h4>
            <p class="text-muted mb-0">Input permintaan bahan baku baru</p>
        </div>
        <a href="{{ route('permintaan-bahan-baku.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('permintaan-bahan-baku.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nomor Permintaan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nomor_permintaan') is-invalid @enderror" 
                               name="nomor_permintaan"
                               value="{{ old('nomor_permintaan', $nomor_permintaan) }}"
                               readonly>
                        <small class="text-muted">Nomor otomatis</small>
                        @error('nomor_permintaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Permintaan <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_permintaan') is-invalid @enderror" 
                               name="tanggal_permintaan" 
                               value="{{ old('tanggal_permintaan', date('Y-m-d')) }}"
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
                          rows="3"
                          placeholder="Keterangan atau alasan permintaan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi:</strong>
                <ul class="mb-0 mt-2">
                    <li>Permintaan akan masuk dengan status <strong>Pending</strong></li>
                    <li>Admin perlu <strong>menyetujui</strong> permintaan terlebih dahulu</li>
                    <li>Setelah disetujui, lakukan <strong>Penerimaan Bahan Baku</strong> untuk update stok</li>
                </ul>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Permintaan
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
        const tableBody = document.querySelector('#tableBahan tbody');
        const btnTambah = document.getElementById('btnTambahBahan');
        let rowCount = 0;

        function addRow() {
            const tr = document.createElement('tr');
            
            let optionsHtml = '<option value="">-- Pilih Bahan Baku --</option>';
            bahanBakuData.forEach(bahan => {
                const satuanBeliVal = bahan.satuan_beli || bahan.satuan;
                const isiPerKemasanVal = bahan.isi_per_kemasan || 1;
                optionsHtml += `<option value="${bahan.id}" data-satuan="${bahan.satuan}" data-satuan-beli="${satuanBeliVal}" data-isi="${isiPerKemasanVal}">${bahan.nama} (${bahan.kode})</option>`;
            });

            tr.innerHTML = `
                <td>
                    <select name="bahan[${rowCount}][id_bahan]" class="form-select select-bahan" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="bahan[${rowCount}][jumlah]" class="form-control input-jumlah" step="0.01" min="0.01" required>
                        <span class="input-group-text satuan-label">Unit</span>
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
            
            let currentSatuanPakai = 'Unit';
            let currentIsi = 1;
            
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
            
            // Event listener hapus
            tr.querySelector('.btn-hapus').addEventListener('click', function() {
                tr.remove();
            });

            rowCount++;
        }

        // Tambah baris pertama otomatis
        addRow();

        btnTambah.addEventListener('click', addRow);
    });
</script>
@endsection