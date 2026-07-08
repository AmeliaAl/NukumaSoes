@extends('layouts.app')

@section('title', 'Input Penerimaan Bahan')
@section('page-title', 'Input Penerimaan Bahan Baku')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Input Penerimaan Bahan Baku</h4>
            <p class="text-muted mb-0">Input penerimaan bahan baku dan update stok (FIFO Method)</p>
        </div>
        <a href="{{ route('penerimaan-bahan-baku.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('penerimaan-bahan-baku.store') }}" method="POST">
            @csrf
            
            <!-- INFO BOX -->
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Dua Cara Penerimaan:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Dari Permintaan:</strong> Klik tombol "Terima" di permintaan atau input nomor permintaan untuk auto-fill bahan baku</li>
                    <li><strong>Langsung:</strong> Input semua data secara manual tanpa permintaan</li>
                    <li><strong>NEW:</strong> Satu permintaan bisa diterima berkali-kali hingga jumlahnya terpenuhi!</li>
                </ul>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nomor Penerimaan <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nomor_penerimaan') is-invalid @enderror" 
                               name="nomor_penerimaan"
                               value="{{ old('nomor_penerimaan', $nomor_penerimaan) }}"
                               readonly>
                        <small class="text-muted">Nomor otomatis</small>
                        @error('nomor_penerimaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('tanggal_penerimaan') is-invalid @enderror" 
                               name="tanggal_penerimaan" 
                               value="{{ old('tanggal_penerimaan', date('Y-m-d')) }}"
                               required>
                        @error('tanggal_penerimaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- NOMOR PERMINTAAN - OPSIONAL -->
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label">
                                <i class="fas fa-link me-2"></i>Nomor Permintaan (Opsional)
                            </label>
                            <input type="text" 
                                   class="form-control @error('nomor_permintaan_cari') is-invalid @enderror" 
                                   id="nomorPermintaanInput"
                                   placeholder="Contoh: PBB-20260117-001"
                                   value="{{ $nomorPermintaanAuto ?? old('nomor_permintaan_cari') }}">
                            <small class="text-muted">Kosongkan jika penerimaan langsung tanpa permintaan</small>
                            @error('nomor_permintaan_cari')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="cariPermintaan()">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                    </div>
                    
                    <!-- Info Permintaan -->
                    <div id="infoPermintaan" class="mt-3" style="display: {{ $permintaanData ? 'block' : 'none' }};">
                        <div class="alert alert-success mb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Permintaan Ditemukan!</strong><br>
                                    <small id="detailPermintaan">
                                        @if($permintaanData)
                                            <strong>{{ $permintaanData['nomor_permintaan'] }}</strong> - Tanggal: {{ $permintaanData['tanggal_permintaan'] }}<br>
                                            Total Item: {{ count($permintaanData['details']) }}<br>
                                        @endif
                                    </small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusPermintaan()">
                                    <i class="fas fa-times"></i> Hapus Link
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="id_permintaan_bahan" id="idPermintaanHidden" value="{{ $permintaanData['id_permintaan_bahan'] ?? '' }}">
                </div>
            </div>

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
                       value="{{ old('supplier') }}"
                       placeholder="Contoh: PT. Sumber Rezeki (opsional)">
                @error('supplier')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          name="keterangan" 
                          rows="3"
                          placeholder="Keterangan penerimaan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-success">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Penerimaan Bahan Baku (FIFO):</strong>
                <ul class="mb-0 mt-2">
                    <li>Stok akan <strong>otomatis bertambah</strong> sesuai jumlah diterima</li>
                    <li>Data penerimaan akan masuk ke <strong>Stok Batch FIFO</strong></li>
                    <li>Batch ini akan digunakan saat <strong>Pemakaian Bahan</strong> (First In First Out)</li>
                    <li>Harga rata-rata bahan akan <strong>dikalkulasi ulang</strong></li>
                    <li><strong>NEW:</strong> Jurnal akuntansi akan dibuat otomatis menggunakan Total Semua Biaya</li>
                </ul>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Penerimaan
                </button>
                <a href="{{ route('penerimaan-bahan-baku.index') }}" class="btn btn-secondary">
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

    // Data dari permintaaan (jika via redirect)
    const initPermintaanData = @json($permintaanData);

    function addRow(data = {}) {
        const id_bahan = data.id_bahan || '';
        const id_permintaan_detail = data.id_permintaan_detail || '';
        const jumlah_diterima = data.jumlah_diterima || '';
        const sisa_belum_diterima = data.sisa_belum_diterima || '';
        const satuan = data.satuan || 'Unit';

        const tr = document.createElement('tr');
        
        let optionsHtml = '<option value="">-- Pilih Bahan Baku --</option>';
        bahanBakuData.forEach(bahan => {
            const isSelected = (bahan.id == id_bahan) ? 'selected' : '';
            const satuanBeliVal = bahan.satuan_beli || bahan.satuan;
            const isiPerKemasanVal = bahan.isi_per_kemasan || 1;
            optionsHtml += `<option value="${bahan.id}" data-satuan="${bahan.satuan}" data-satuan-beli="${satuanBeliVal}" data-isi="${isiPerKemasanVal}" ${isSelected}>${bahan.nama} (${bahan.kode})</option>`;
        });

        // Data Default (Jika dari master/permintaan)
        let defaultJumlahBeli = jumlah_diterima;
        let defaultIsi = data.isi_per_kemasan || 1;
        let defaultSatuanBeli = data.satuan_beli || data.satuan || 'Unit';
        
        if (sisa_belum_diterima !== '') {
            // Konversi dari satuan pakai ke satuan beli
            defaultJumlahBeli = sisa_belum_diterima / defaultIsi;
        }

        // Jika dari permintaan, set max dan hint
        let maxAttr = sisa_belum_diterima !== '' ? `max="${sisa_belum_diterima / defaultIsi}"` : '';
        let hintHtml = sisa_belum_diterima !== '' ? `<br><small class="text-danger">Sisa Permintaan: ${sisa_belum_diterima} ${data.satuan || ''}</small><br><small class="text-muted convert-hint">Setara dengan ${defaultJumlahBeli} ${defaultSatuanBeli}</small>` : '<br><small class="text-muted convert-hint"></small>';

        tr.innerHTML = `
            <td>
                <select name="bahan[${rowCount}][id_bahan]" class="form-select select-bahan" required>
                    ${optionsHtml}
                </select>
                <input type="hidden" name="bahan[${rowCount}][id_permintaan_detail]" value="${id_permintaan_detail}">
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="bahan[${rowCount}][jumlah_diterima]" class="form-control input-jumlah" step="0.01" min="0.01" ${maxAttr} value="${defaultJumlahBeli}" required>
                    <span class="input-group-text satuan-label">${defaultSatuanBeli}</span>
                </div>
                ${hintHtml}
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="bahan[${rowCount}][harga_per_satuan]" class="form-control input-harga" step="100" min="0" required>
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
        
        // Event listeners
        const selectElement = tr.querySelector('.select-bahan');
        const satuanLabel = tr.querySelector('.satuan-label');
        const inputJumlah = tr.querySelector('.input-jumlah');
        const inputHarga = tr.querySelector('.input-harga');
        const labelTotal = tr.querySelector('.label-total');
        const hintElement = tr.querySelector('.convert-hint');
        
        let currentSatuanPakai = data.satuan || 'Unit';
        let currentIsi = defaultIsi;
        
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
            if(hintElement && !sisa_belum_diterima) {
                hintElement.innerHTML = `Setara dengan: <strong>${totalPakai} ${currentSatuanPakai}</strong>`;
            }
        };

        const calcTotal = () => {
            const jml = parseFloat(inputJumlah.value) || 0;
            const hrg = parseFloat(inputHarga.value) || 0;
            const tot = jml * hrg;
            labelTotal.textContent = 'Rp ' + tot.toLocaleString('id-ID');
            calculateGrandTotal();
        };

        inputJumlah.addEventListener('input', () => {
            calcTotal();
            updateHint();
        });
        inputHarga.addEventListener('input', calcTotal);
        
        tr.querySelector('.btn-hapus').addEventListener('click', function() {
            tr.remove();
            calculateGrandTotal();
        });

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

    // Load initial data
    document.addEventListener('DOMContentLoaded', function() {
        if (initPermintaanData && initPermintaanData.details) {
            initPermintaanData.details.forEach(d => {
                if(d.sisa_belum_diterima > 0) {
                    addRow({
                        id_bahan: d.id_bahan,
                        id_permintaan_detail: d.id_permintaan_detail,
                        jumlah_diterima: d.sisa_belum_diterima,
                        sisa_belum_diterima: d.sisa_belum_diterima,
                        satuan: d.satuan,
                        satuan_beli: d.satuan_beli,
                        isi_per_kemasan: d.isi_per_kemasan
                    });
                }
            });
            if(tableBody.children.length === 0) addRow();
        } else {
            addRow();
        }

        btnTambah.addEventListener('click', () => addRow());
    });

    function cariPermintaan() {
        var nomor = document.getElementById('nomorPermintaanInput').value;
        
        if (!nomor) {
            Swal.fire('Perhatian', 'Masukkan nomor permintaan terlebih dahulu', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Mencari...',
            text: 'Sedang mencari data permintaan',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        fetch('/api/permintaan-bahan-baku/cari?nomor=' + nomor)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    document.getElementById('idPermintaanHidden').value = data.permintaan.id_permintaan_bahan;
                    
                    document.getElementById('detailPermintaan').innerHTML = 
                        '<strong>' + data.permintaan.nomor_permintaan + '</strong> - Tanggal: ' + data.permintaan.tanggal_permintaan + '<br>' +
                        'Total Item: ' + data.details.length + '<br>';
                        
                    document.getElementById('infoPermintaan').style.display = 'block';
                    
                    // Reset table and add rows
                    tableBody.innerHTML = '';
                    rowCount = 0;
                    
                    let countAdded = 0;
                    data.details.forEach(d => {
                        if (d.sisa_belum_diterima > 0) {
                            addRow({
                                id_bahan: d.id_bahan,
                                id_permintaan_detail: d.id_permintaan_detail,
                                jumlah_diterima: d.sisa_belum_diterima,
                                sisa_belum_diterima: d.sisa_belum_diterima,
                                satuan: d.satuan,
                                satuan_beli: d.satuan_beli,
                                isi_per_kemasan: d.isi_per_kemasan
                            });
                            countAdded++;
                        }
                    });

                    if(countAdded === 0) {
                        Swal.fire('Info', 'Semua bahan dari permintaan ini sudah diterima sepenuhnya.', 'info');
                        addRow();
                    } else {
                        Swal.fire('Berhasil!', 'Data permintaan ditemukan dan bahan ditambahkan.', 'success');
                    }
                    calculateGrandTotal();
                } else {
                    document.getElementById('infoPermintaan').style.display = 'none';
                    Swal.fire('Tidak Ditemukan', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            });
    }

    function hapusPermintaan() {
        document.getElementById('nomorPermintaanInput').value = '';
        document.getElementById('idPermintaanHidden').value = '';
        document.getElementById('infoPermintaan').style.display = 'none';
        
        tableBody.innerHTML = '';
        rowCount = 0;
        addRow();
        calculateGrandTotal();
        
        Swal.fire({
            icon: 'success',
            title: 'Data Permintaan Dihapus',
            timer: 1500,
            showConfirmButton: false
        });
    }
</script>
@endsection