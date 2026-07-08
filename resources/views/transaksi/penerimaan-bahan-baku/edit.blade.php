@extends('layouts.app')

@section('title', 'Edit Penerimaan Bahan')
@section('page-title', 'Edit Penerimaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Edit Penerimaan Bahan Baku</h4>
            <p class="text-muted mb-0">Update data penerimaan bahan baku</p>
        </div>
        <a href="{{ route('penerimaan-bahan-baku.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('penerimaan-bahan-baku.update', $penerimaan->id_penerimaan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nomor Penerimaan</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $penerimaan->nomor_penerimaan }}"
                               readonly>
                        <small class="text-muted">Nomor tidak dapat diubah</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_penerimaan') is-invalid @enderror" 
                               name="tanggal_penerimaan" 
                               value="{{ old('tanggal_penerimaan', $penerimaan->tanggal_penerimaan->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_penerimaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            @if($penerimaan->permintaanBahanBaku)
            <div class="alert alert-info mb-3">
                <i class="fas fa-link me-2"></i>
                <strong>Dari Permintaan:</strong> {{ $penerimaan->permintaanBahanBaku->nomor_permintaan }}
            </div>
            @endif
            
            <!-- TABEL BAHAN BAKU DINAMIS -->
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Bahan Baku Diterima</h5>
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
                                    <th width="180">Jumlah <span class="text-danger">*</span></th>
                                    <th width="200">Harga Satuan <span class="text-danger">*</span></th>
                                    <th width="200">Total Biaya</th>
                                    <th width="80" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Baris akan ditambahkan via JavaScript -->
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total Semua Biaya</th>
                                    <th id="totalSemuaBiaya" class="text-end fw-bold text-success">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @error('bahan')
                        <div class="text-danger p-2"><small>{{ $message }}</small></div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Supplier</label>
                <input type="text" 
                       class="form-control @error('supplier') is-invalid @enderror" 
                       name="supplier" 
                       value="{{ old('supplier', $penerimaan->supplier) }}"
                       placeholder="Nama supplier (opsional)">
                @error('supplier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="3">{{ old('keterangan', $penerimaan->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian:</strong> Perubahan data penerimaan akan mempengaruhi stok batch FIFO dan harga rata-rata bahan baku.
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update
                </button>
                <a href="{{ route('penerimaan-bahan-baku.show', $penerimaan->id_penerimaan) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const bahanBakuData = {!! $bahanBakuJson !!};
    const tableBody = document.querySelector('#tableBahan tbody');
    const btnTambah = document.getElementById('btnTambahBahan');
    let rowCount = 0;

    const existingDetails = {!! json_encode($penerimaan->details->map(function($d) {
        return [
            'id_penerimaan_detail' => $d->id_penerimaan_detail,
            'id_bahan' => $d->id_bahan,
            'id_permintaan_detail' => $d->id_permintaan_detail,
            'jumlah_diterima' => $d->jumlah_diterima,
            'harga_per_satuan' => $d->harga_per_satuan,
            'satuan' => $d->bahanBaku->satuan
        ];
    })) !!};

    function addRow(data = {}) {
        const id_penerimaan_detail = data.id_penerimaan_detail || '';
        const id_bahan = data.id_bahan || '';
        const id_permintaan_detail = data.id_permintaan_detail || '';
        const jumlah_diterima = data.jumlah_diterima || '';
        const harga_per_satuan = data.harga_per_satuan || '';
        const satuan = data.satuan || 'Unit';

        const tr = document.createElement('tr');
        
        let optionsHtml = '<option value="">-- Pilih Bahan Baku --</option>';
        bahanBakuData.forEach(bahan => {
            const isSelected = (bahan.id == id_bahan) ? 'selected' : '';
            optionsHtml += `<option value="${bahan.id}" data-satuan="${bahan.satuan}" ${isSelected}>${bahan.nama} (${bahan.kode})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="bahan[${rowCount}][id_bahan]" class="form-select select-bahan" required>
                    ${optionsHtml}
                </select>
                <input type="hidden" name="bahan[${rowCount}][id_penerimaan_detail]" value="${id_penerimaan_detail}">
                <input type="hidden" name="bahan[${rowCount}][id_permintaan_detail]" value="${id_permintaan_detail}">
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="bahan[${rowCount}][jumlah_diterima]" class="form-control input-jumlah" step="0.01" min="0.01" value="${jumlah_diterima}" required>
                    <span class="input-group-text satuan-label">${satuan}</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="bahan[${rowCount}][harga_per_satuan]" class="form-control input-harga" step="100" min="0" value="${harga_per_satuan}" required>
                </div>
            </td>
            <td class="text-end align-middle">
                <span class="text-success fw-bold label-total">Rp 0</span>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-danger btn-sm btn-hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(tr);
        
        const selectElement = tr.querySelector('.select-bahan');
        const satuanLabel = tr.querySelector('.satuan-label');
        const inputJumlah = tr.querySelector('.input-jumlah');
        const inputHarga = tr.querySelector('.input-harga');
        const labelTotal = tr.querySelector('.label-total');
        
        selectElement.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const newSatuan = selectedOption.getAttribute('data-satuan');
            satuanLabel.textContent = newSatuan || 'Unit';
        });

        const calcTotal = () => {
            const jml = parseFloat(inputJumlah.value) || 0;
            const hrg = parseFloat(inputHarga.value) || 0;
            const tot = jml * hrg;
            labelTotal.textContent = 'Rp ' + tot.toLocaleString('id-ID');
            calculateGrandTotal();
        };

        inputJumlah.addEventListener('input', calcTotal);
        inputHarga.addEventListener('input', calcTotal);
        
        tr.querySelector('.btn-hapus').addEventListener('click', function() {
            tr.remove();
            calculateGrandTotal();
        });

        // initial calculation
        calcTotal();

        rowCount++;
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('#tableBahan tbody tr').forEach(tr => {
            const jml = parseFloat(tr.querySelector('.input-jumlah').value) || 0;
            const hrg = parseFloat(tr.querySelector('.input-harga').value) || 0;
            grandTotal += (jml * hrg);
        });
        document.getElementById('totalSemuaBiaya').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (existingDetails && existingDetails.length > 0) {
            existingDetails.forEach(d => {
                addRow(d);
            });
        } else {
            addRow();
        }

        btnTambah.addEventListener('click', () => addRow());
    });
</script>
@endsection