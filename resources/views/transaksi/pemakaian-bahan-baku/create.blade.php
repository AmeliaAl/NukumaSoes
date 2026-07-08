@extends('layouts.app')

@section('title', 'Input Pemakaian Bahan')
@section('page-title', 'Input Pemakaian Bahan Baku (FIFO)')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Input Pemakaian Bahan Baku (Multiple Items)</h4>
            <p class="text-muted mb-0">Input pemakaian banyak bahan baku sekaligus dengan metode FIFO</p>
        </div>
        <a href="{{ route('pemakaian-bahan-baku.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>Error!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('pemakaian-bahan-baku.store') }}" method="POST" id="pemakaianForm">
            @csrf
            
            <!-- Job Order Selection -->
            <div class="row mb-4">
                <div class="col-md-12">
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

            <!-- Dynamic Items Container -->
            <div id="itemsContainer">
                <!-- Item rows akan ditambahkan di sini -->
            </div>

            <!-- Add Item Button -->
            <div class="mb-3">
                <button type="button" class="btn btn-success" id="addItemBtn">
                    <i class="fas fa-plus me-2"></i>Tambah Bahan Baku
                </button>
            </div>

            <!-- Total Summary -->
            <div class="card bg-light mb-3" id="summaryCard" style="display: none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Total Item:</strong>
                            <h4 class="text-primary mb-0" id="totalItems">0</h4>
                        </div>
                        <div class="col-md-8">
                            <strong>Estimasi Total Biaya (berdasarkan FIFO):</strong>
                            <h4 class="text-success mb-0" id="totalBiaya">Rp 0</h4>
                            <small class="text-muted">*Biaya aktual dihitung otomatis saat menyimpan berdasarkan harga FIFO</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi Penting - Metode FIFO:</strong>
                <ul class="mb-0 mt-2">
                    <li>Anda bisa <strong>menambahkan multiple bahan baku</strong> sekaligus untuk job order ini</li>
                    <li>Sistem akan <strong>otomatis mengambil stok dari batch yang paling lama masuk</strong> (FIFO)</li>
                    <li>Jika stok dari 1 batch tidak cukup, akan <strong>otomatis split ke batch berikutnya</strong></li>
                    <li>Harga akan dihitung berdasarkan <strong>harga per batch FIFO</strong></li>
                    <li>Pastikan <strong>jumlah pakai tidak melebihi stok tersedia</strong> untuk setiap bahan</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save me-2"></i>Simpan Semua Pemakaian Bahan
                </button>
                <a href="{{ route('pemakaian-bahan-baku.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Template untuk item row (hidden) -->
<template id="itemTemplate">
    <div class="card mb-3 item-row" data-index="INDEX_PLACEHOLDER">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-box me-2"></i>Bahan Baku #<span class="item-number">INDEX_PLACEHOLDER</span></h6>
                <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Bahan Baku <span class="text-danger">*</span></label>
                    <select class="form-select bahan-select" 
                            name="items[INDEX_PLACEHOLDER][id_bahan]" 
                            data-index="INDEX_PLACEHOLDER"
                            required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach($options as $opt)
                            <option value="{{ $opt->id }}" 
                                    data-satuan="{{ $opt->satuan }}"
                                    data-stok="{{ $opt->stok }}"
                                    data-kode="{{ $opt->kode }}">
                                {{ $opt->nama }}
                                (Stok: {{ number_format($opt->stok, 2) }} {{ $opt->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Jumlah Pakai <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               class="form-control jumlah-input" 
                               name="items[INDEX_PLACEHOLDER][jumlah_pakai]" 
                               data-index="INDEX_PLACEHOLDER"
                               min="0.01"
                               step="0.01"
                               placeholder="0"
                               required>
                        <span class="input-group-text satuan-text">Unit</span>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Stok Tersedia</label>
                    <input type="text" 
                           class="form-control stok-text" 
                           readonly
                           placeholder="-">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Sisa Setelah Pakai</label>
                    <input type="text" 
                           class="form-control sisa-text" 
                           readonly
                           placeholder="-">
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-8">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <input type="text" 
                           class="form-control" 
                           name="items[INDEX_PLACEHOLDER][keterangan]" 
                           placeholder="Contoh: Untuk produksi batch A">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estimasi Biaya Item (FIFO)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-success">Rp</span>
                        <input type="text" class="form-control item-biaya-input fw-bold text-success bg-white" readonly placeholder="0">
                    </div>
                </div>
            </div>
            
            <!-- FIFO Batch Info (akan diisi via AJAX) -->
            <div class="fifo-batch-info mt-3 d-none">
                <div class="alert alert-sm alert-info mb-0 py-2">
                    <small><strong><i class="fas fa-layer-group me-1"></i> FIFO Batches Tersedia:</strong></small>
                    <div class="batch-list mt-1"></div>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
    // VERSION: 2.0.3 - TIMESTAMP TRACKING
    (function() {
        const loadTime = new Date().toISOString();
        console.log('%c=== SCRIPT VERSION 2.0.3 LOADED ===', 'background: #222; color: #bada55; font-size: 16px; font-weight: bold;');
        console.log(`Load timestamp: ${loadTime}`);
        console.log(`Window location: ${window.location.href}`);
        
        // Check if already loaded
        if (window.pemakaianBahanScriptLoaded) {
            console.error('⚠️ DUPLICATE SCRIPT LOAD DETECTED! This script was already loaded!');
            return;
        }
        window.pemakaianBahanScriptLoaded = true;
    })();
    
    let itemIndex = 0;
    let items = [];
    let batchLoadQueue = {}; 
    let ajaxControllers = {}; 
    let itemsFullyInitialized = {};
    
    // Store in window for debugging
    window.debugPemakaianBahan = {
        itemIndex: () => itemIndex,
        items: () => items,
        batchLoadQueue: () => batchLoadQueue,
        version: '2.0.3'
    };
    
    // ============================================================================
    // CORE CALCULATION FUNCTIONS - MUST BE DEFINED FIRST
    // ============================================================================
    
    // RENAMED TO PREVENT OVERRIDE
    function calculateEstimatedTotalV2(index) {
        console.log(`%c[CALC ESTIMATE V2] ===== Item #${index} START CALCULATION =====`, 'color: #00ff00; font-weight: bold; font-size: 14px;');
        console.log(`[CALC ESTIMATE V2] Item #${index} - Function version: 2.0.3`);
        
        const card = $(`[data-index="${index}"]`);
        
        if (card.length === 0) {
            console.error(`[CALC ESTIMATE V2] Item #${index} - ERROR: Card not found in DOM!`);
            return;
        }
        
        const idBahan = card.find('.bahan-select').val();
        const jumlahInput = card.find('.jumlah-input');
        const jumlah = parseFloat(jumlahInput.val()) || 0;
        const batches = card.data('batches') || [];
        const estimasiBox = card.find('.estimasi-biaya'); // Might be empty now
        const biayaInput = card.find('.item-biaya-input');

        console.log(`[CALC ESTIMATE V2] Item #${index} - Bahan ID: "${idBahan}"`);
        console.log(`[CALC ESTIMATE V2] Item #${index} - Jumlah input value: "${jumlahInput.val()}"`);
        console.log(`[CALC ESTIMATE V2] Item #${index} - Jumlah parsed: ${jumlah}`);
        console.log(`[CALC ESTIMATE V2] Item #${index} - Batches count: ${batches.length}`);
        console.log(`[CALC ESTIMATE V2] Item #${index} - Batches array:`, batches);
        console.log(`[CALC ESTIMATE V2] Item #${index} - Estimasi box exists: ${estimasiBox.length > 0}`);

        // Handle no bahan selected
        if (!idBahan || idBahan === '') {
            console.log(`[CALC ESTIMATE V2] Item #${index} - No bahan selected, resetting estimasi`);
            card.attr('data-estimasi', '0');
            biayaInput.val('0');
            updateGrandTotal();
            console.log(`[CALC ESTIMATE V2] ===== Item #${index} END (no bahan) =====`);
            return;
        }

        // Handle no batches
        if (batches.length === 0) {
            console.log(`[CALC ESTIMATE V2] Item #${index} - No batches available`);
            card.attr('data-estimasi', '0');
            biayaInput.val('0');
            
            updateGrandTotal();
            console.log(`[CALC ESTIMATE V2] ===== Item #${index} END (no batches) =====`);
            return;
        }

        // Handle zero quantity
        if (jumlah <= 0) {
            console.log(`[CALC ESTIMATE V2] Item #${index} - Zero quantity, resetting to 0`);
            card.attr('data-estimasi', '0');
            biayaInput.val('0');
            
            updateGrandTotal();
            console.log(`[CALC ESTIMATE V2] ===== Item #${index} END (zero qty) =====`);
            return;
        }

        // Calculate FIFO cost
        let sisaKebutuhan = jumlah;
        let estimasiBiaya = 0;
        let calculationDetails = [];

        console.log(`[CALC ESTIMATE V2] Item #${index} - ===== Starting FIFO calculation =====`);
        
        batches.forEach((batch, idx) => {
            if (sisaKebutuhan <= 0) {
                console.log(`[CALC ESTIMATE V2] Item #${index} - Batch ${idx + 1}: SKIPPED (no remaining need)`);
                return;
            }
            
            const sisaStok = parseFloat(batch.sisa_stok) || 0;
            const harga = parseFloat(batch.harga_per_satuan) || 0;
            const ambil = Math.min(sisaKebutuhan, sisaStok);
            
            const biayaBatch = ambil * harga;
            estimasiBiaya += biayaBatch;
            sisaKebutuhan -= ambil;
            
            calculationDetails.push(`Batch #${batch.id_stok}: ${ambil} × Rp ${harga.toLocaleString('id-ID')} = Rp ${biayaBatch.toLocaleString('id-ID')}`);
            
            console.log(`[CALC ESTIMATE V2] Item #${index} - Batch ${idx + 1} (#${batch.id_stok}): Take ${ambil} @ Rp ${harga} = Rp ${biayaBatch}`);
        });

        console.log(`[CALC ESTIMATE V2] Item #${index} - =============================`);
        console.log(`[CALC ESTIMATE V2] Item #${index} - FINAL TOTAL: Rp ${estimasiBiaya}`);
        console.log(`[CALC ESTIMATE V2] Item #${index} - =============================`);

        // Update UI
        card.attr('data-estimasi', estimasiBiaya);
        
        const formattedBiaya = estimasiBiaya.toLocaleString('id-ID');
        console.log(`[CALC ESTIMATE V2] Item #${index} - Formatted text: Rp ${formattedBiaya}`);
        
        biayaInput.val(formattedBiaya);
        
        console.log(`[CALC ESTIMATE V2] Item #${index} - Calculation details: ${calculationDetails.join('; ')}`);

        updateGrandTotal();
        
        console.log(`[CALC ESTIMATE V2] ===== Item #${index} END CALCULATION =====`);
    }
    
    function updateGrandTotal() {
        let grandTotal = 0;
        
        $('.item-row').each(function() {
            const estimasi = parseFloat($(this).attr('data-estimasi')) || 0;
            grandTotal += estimasi;
        });
        
        console.log(`[GRAND TOTAL] Rp ${grandTotal.toLocaleString('id-ID')}`);
        
        $('#totalBiaya').text('Rp ' + grandTotal.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }));
    }
    
    // ============================================================================
    // INITIALIZATION AND ITEM MANAGEMENT
    // ============================================================================
    
    $(document).ready(function() {
        console.log('=== PEMAKAIAN BAHAN FORM INITIALIZED ===');
        console.log('jQuery version:', $.fn.jquery);
        
        // Pastikan function sudah ready sebelum add item
        if (typeof calculateEstimatedTotalV2 !== 'function') {
            console.error('ERROR: calculateEstimatedTotalV2 not defined!');
        } else {
            console.log('✓ calculateEstimatedTotalV2 is ready');
        }
        
        // Pastikan page dan jQuery fully loaded
        setTimeout(function() {
            console.log('[INIT] Page ready...');
            const initialJob = $('#jobSelect').val();
            if (initialJob) {
                console.log('[INIT] Found initial Job Order, triggering BOM load...');
                $('#jobSelect').trigger('change');
            } else {
                console.log('[INIT] No initial Job Order, adding first item...');
                addItem();
            }
        }, 500); // Increased delay untuk item pertama
        
        $('#addItemBtn').click(function() {
            console.log('[ADD BTN] Add item button clicked');
            addItem();
        });

        // Event listener untuk auto-fill BOM saat Job Order dipilih
        $('#jobSelect').change(function() {
            const jobId = $(this).val();
            if (!jobId) return;

            console.log(`[JOB SELECT] Fetching BOM for job: ${jobId}`);
            
            // Tampilkan loading overlay atau text
            const container = $('#itemsContainer');
            container.html('<div class="text-center py-4 text-primary"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><br>Memuat resep bahan (BOM)...</div>');
            
            // Disable tombol simpan sementara
            $('#submitBtn').prop('disabled', true);

            $.ajax({
                url: `/pemakaian-bahan-baku/get-bom-for-job/${jobId}`,
                method: 'GET',
                success: function(response) {
                    console.log('[JOB SELECT] BOM fetch success', response);
                    
                    // Bersihkan container
                    container.empty();
                    
                    // Reset array tracking
                    items = [];
                    itemIndex = 0;
                    batchLoadQueue = {};
                    itemsFullyInitialized = {};
                    
                    // Cancel active AJAX requests
                    Object.values(ajaxControllers).forEach(controller => controller.abort());
                    ajaxControllers = {};

                    if (response.success && response.data && response.data.length > 0) {
                        let delay = 0;
                        let adaWarning = false;
                        
                        response.data.forEach((bomItem, idx) => {
                            // Delay penambahan item agar rendering stabil
                            setTimeout(() => {
                                console.log(`[JOB SELECT] Auto-adding BOM item:`, bomItem);
                                addItem(bomItem.id_bahan, bomItem.jumlah_kebutuhan);
                                
                                // Jika ini adalah item terakhir, aktifkan tombol
                                if (idx === response.data.length - 1) {
                                    setTimeout(() => {
                                        $('#submitBtn').prop('disabled', false);
                                    }, 1000);
                                }
                            }, delay);
                            delay += 500; // Jeda setengah detik per item untuk mencegah race condition UI
                            
                            if (!bomItem.cukup) {
                                adaWarning = true;
                            }
                        });
                        
                        if (adaWarning) {
                            Swal.fire({
                                title: 'Perhatian!',
                                text: 'Beberapa bahan baku dari resep (BOM) tidak memiliki stok yang cukup. Harap periksa peringatan pada baris berwarna merah.',
                                icon: 'warning'
                            });
                        }
                    } else {
                        // Produk tidak punya BOM
                        Swal.fire({
                            title: 'Info',
                            text: 'Produk untuk Job Order ini tidak memiliki data BOM (Resep). Silakan tambahkan bahan secara manual.',
                            icon: 'info'
                        });
                        addItem();
                        $('#submitBtn').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('[JOB SELECT] BOM fetch error:', error);
                    container.empty();
                    addItem();
                    $('#submitBtn').prop('disabled', false);
                    Swal.fire('Error', 'Gagal memuat resep bahan baku', 'error');
                }
            });
        });
        
        $('#pemakaianForm').submit(function(e) {
            console.log('[SUBMIT] Form submit triggered');
            
            let validItems = 0;
            $('.item-row').each(function() {
                const bahan = $(this).find('.bahan-select').val();
                const jumlah = parseFloat($(this).find('.jumlah-input').val()) || 0;
                if (bahan && jumlah > 0) {
                    validItems++;
                }
            });

            if (validItems === 0) {
                e.preventDefault();
                Swal.fire('Error', 'Minimal harus ada 1 bahan baku yang valid!', 'error');
                return false;
            }
            
            let stokTidakCukup = false;
            let errorMessage = '';
            $('.item-row').each(function() {
                const bahanSelect = $(this).find('.bahan-select');
                const selectedOption = bahanSelect.find(':selected');
                const stok = parseFloat(selectedOption.data('stok')) || 0;
                const jumlah = parseFloat($(this).find('.jumlah-input').val()) || 0;
                const namaBahan = selectedOption.text().split('(')[0].trim();
                
                if (jumlah > stok) {
                    stokTidakCukup = true;
                    errorMessage = `Stok ${namaBahan} tidak mencukupi! (Dibutuhkan: ${jumlah}, Tersedia: ${stok})`;
                    return false;
                }
            });

            if (stokTidakCukup) {
                e.preventDefault();
                Swal.fire('Error', errorMessage, 'error');
                return false;
            }
            
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Penyimpanan',
                html: `Anda akan menyimpan <strong>${validItems} bahan baku</strong> untuk job order ini.<br>Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('[SUBMIT] Confirmed, submitting form...');
                    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');
                    $(this).off('submit').submit();
                }
            });
        });
    });
    
    function addItem(defaultIdBahan = '', defaultJumlah = '') {
        itemIndex++;
        const currentIndex = itemIndex; // Store untuk closure
        
        console.log(`[ADD ITEM] ========== Adding item #${currentIndex} ==========`);
        
        const template = document.getElementById('itemTemplate').content.cloneNode(true);
        const itemCard = template.querySelector('.item-row');
        
        // Replace semua INDEX_PLACEHOLDER
        itemCard.querySelectorAll('[name],[data-index]').forEach(el => {
            if (el.name) {
                el.name = el.name.replaceAll('INDEX_PLACEHOLDER', currentIndex);
            }
            if (el.dataset.index) {
                el.dataset.index = currentIndex;
            }
        });

        itemCard.querySelectorAll('.item-number').forEach(el => {
            el.textContent = currentIndex;
        });

        if (defaultIdBahan) {
            itemCard.querySelector('.bahan-select').value = defaultIdBahan;
        }
        if (defaultJumlah) {
            itemCard.querySelector('.jumlah-input').value = defaultJumlah;
        }

        // Set attributes
        itemCard.setAttribute('data-index', currentIndex);
        itemCard.setAttribute('data-estimasi', '0');
        itemCard.setAttribute('data-batches-loaded', 'false');
        itemCard.setAttribute('data-batches-loading', 'false');
        
        // Append ke container
        document.getElementById('itemsContainer').appendChild(itemCard);
        
        // Track item
        items.push(currentIndex);
        itemsFullyInitialized[currentIndex] = false;
        
        updateSummary();

        // CRITICAL: Delay berbeda untuk item pertama vs item lainnya
        const isFirstItem = currentIndex === 1;
        const attachDelay = isFirstItem ? 400 : 250;
        
        console.log(`[ADD ITEM] Item #${currentIndex} - Is first item: ${isFirstItem}, Using delay: ${attachDelay}ms`);

        // Attach event listeners dengan delay
        setTimeout(function() {
            console.log(`[ADD ITEM] Item #${currentIndex} - ===== Starting attachment =====`);
            attachEventListeners(currentIndex);

            const card = $(`[data-index="${currentIndex}"]`);
            
            if (card.length === 0) {
                console.error(`[ADD ITEM] Item #${currentIndex} - ERROR: Card not found in DOM!`);
                return;
            }
            
            const select = card.find('.bahan-select');
            
            if (select.length === 0) {
                console.error(`[ADD ITEM] Item #${currentIndex} - ERROR: Select not found!`);
                return;
            }
            
            // Mark as manually initialized
            select.data('initialized', true);
            
            console.log(`[ADD ITEM] Item #${currentIndex} - Select found, marked as initialized`);

            // Finalize initialization dengan delay berbeda untuk item pertama
            const initDelay = isFirstItem ? 800 : 400;
            
            console.log(`[ADD ITEM] Item #${currentIndex} - Will finalize in ${initDelay}ms`);
            
            setTimeout(function() {
                itemsFullyInitialized[currentIndex] = true;
                console.log(`[ADD ITEM] Item #${currentIndex} - ===== FULLY INITIALIZED =====`);

                // Trigger FIFO load jika ada bahan terpilih
                const idBahan = select.val();
                console.log(`[ADD ITEM] Item #${currentIndex} - Current selected bahan: ${idBahan}`);
                
                if (idBahan) {
                    console.log(`[ADD ITEM] Item #${currentIndex} - ===== AUTO-TRIGGERING FIFO LOAD =====`);
                    handleBahanChange(currentIndex, idBahan);
                } else {
                    console.log(`[ADD ITEM] Item #${currentIndex} - No bahan selected yet`);
                }
            }, initDelay);

        }, attachDelay);
    }
    
    function removeItem(index) {
        console.log(`[REMOVE ITEM] Removing item #${index}`);
        
        const card = $(`[data-index="${index}"]`);
        
        // Cleanup mutation observer
        const observer = card.data('estimasiObserver');
        if (observer) {
            observer.disconnect();
            console.log(`[REMOVE ITEM] Item #${index} - Mutation observer disconnected`);
        }
        
        // Cancel ongoing AJAX
        if (ajaxControllers[index]) {
            ajaxControllers[index].abort();
            delete ajaxControllers[index];
        }
        
        // Remove from DOM
        card.remove();
        
        // Remove from tracking
        items = items.filter(i => i !== index);
        
        if (batchLoadQueue[index]) {
            delete batchLoadQueue[index];
        }
        
        if (itemsFullyInitialized[index]) {
            delete itemsFullyInitialized[index];
        }
        
        renumberItems();
        updateSummary();
        updateGrandTotal();
    }
    
    function attachEventListeners(index) {
        const card = $(`[data-index="${index}"]`);
        
        if (card.length === 0) {
            console.error(`[ATTACH] Card not found for index: ${index}`);
            return;
        }
        
        console.log(`[ATTACH] Attaching listeners to item #${index}`);
        
        // Remove button
        card.find('.remove-item-btn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log(`[REMOVE BTN] Clicked for item #${index}`);
            
            if (items.length <= 1) {
                Swal.fire('Perhatian', 'Minimal harus ada 1 bahan baku!', 'warning');
                return;
            }
            removeItem(index);
        });
        
        // Bahan select change
        const bahanSelect = card.find('.bahan-select');
        
        bahanSelect.off('change').on('change', function(e) {
            const selectedValue = $(this).val();
            console.log(`[BAHAN SELECT] Item #${index} - Changed to: ${selectedValue}`);
            
            // Check if fully initialized
            if (!itemsFullyInitialized[index]) {
                console.log(`[BAHAN SELECT] Item #${index} - Not fully initialized yet, queuing...`);
                
                // Retry setelah initialization complete
                setTimeout(function() {
                    if (itemsFullyInitialized[index] && bahanSelect.val() === selectedValue) {
                        console.log(`[BAHAN SELECT] Item #${index} - Retry after init`);
                        handleBahanChange(index, selectedValue);
                    }
                }, 500);
                
                return;
            }
            
            handleBahanChange(index, selectedValue);
        });
        
        // Jumlah input change dengan debounce
        card.find('.jumlah-input').off('input').on('input', function(e) {
            const value = $(this).val();
            console.log(`[JUMLAH INPUT] Item #${index} - Changed to: ${value}`);
            
            // Delay sedikit untuk debounce
            clearTimeout(card.data('jumlahTimer'));
            card.data('jumlahTimer', setTimeout(function() {
                calculateSisa(index);
            }, 300));
        });
        
        // Observers and listeners successfully attached
        console.log(`[ATTACH] Successfully attached listeners to item #${index}`);
    }
    
    function handleBahanChange(index, selectedValue) {
        const card = $(`[data-index="${index}"]`);
        
        if (card.length === 0) {
            console.error(`[HANDLE CHANGE] Item #${index} - ERROR: Card not found!`);
            return;
        }
        
        const currentLoading = card.attr('data-batches-loading');
        
        console.log(`[HANDLE CHANGE] ===== Item #${index} START =====`);
        console.log(`[HANDLE CHANGE] Item #${index} - Value: ${selectedValue}, Loading: ${currentLoading}`);
        
        // Prevent concurrent loads
        if (currentLoading === 'true') {
            console.log(`[HANDLE CHANGE] Item #${index} - Currently loading, SKIPPING`);
            return;
        }
        
        // Handle empty selection
        if (!selectedValue) {
            console.log(`[HANDLE CHANGE] Item #${index} - Empty selection, hiding estimasi`);
            
            card.find('.item-biaya-input').val('0');
            card.attr('data-estimasi', '0');

            if (batchLoadQueue[index]) {
                delete batchLoadQueue[index];
            }

            updateGrandTotal();
            return;
        }
        
        const bahanSelect = card.find('.bahan-select');
        const selectedOption = bahanSelect.find(':selected');
        const satuan = selectedOption.data('satuan') || 'Unit';
        const stok = parseFloat(selectedOption.data('stok')) || 0;
        const idBahan = selectedValue;
        
        console.log(`[HANDLE CHANGE] Item #${index} - Bahan: ${idBahan}, Satuan: ${satuan}, Stok: ${stok}`);
        
        // Update UI elements
        card.find('.satuan-text').text(satuan);
        card.find('.stok-text').val(stok.toLocaleString('id-ID') + ' ' + satuan);
        
        // Calculate sisa tanpa trigger estimate dulu
        const jumlah = parseFloat(card.find('.jumlah-input').val()) || 0;
        const sisa = stok - jumlah;
        card.find('.sisa-text').val(sisa.toLocaleString('id-ID') + ' ' + satuan);
        
        console.log(`[HANDLE CHANGE] Item #${index} - Jumlah: ${jumlah}, Sisa: ${sisa}`);
        
        const alreadyLoaded = card.attr('data-batches-loaded');
        const isQueued = batchLoadQueue[index];
        
        console.log(`[HANDLE CHANGE] Item #${index} - Already loaded: ${alreadyLoaded}, Queued: ${isQueued}`);
        
        // Load FIFO batches if not already loaded
        if (idBahan && alreadyLoaded !== 'true' && !isQueued) {
            console.log(`[HANDLE CHANGE] Item #${index} - ===== SCHEDULING FIFO BATCH LOAD =====`);
            
            batchLoadQueue[index] = true;
            card.attr('data-batches-loading', 'true');
            
            // CRITICAL: Delay yang lebih lama untuk item pertama
            const isFirstItem = index === 1;
            const ajaxDelay = isFirstItem ? 500 : 350;
            
            console.log(`[HANDLE CHANGE] Item #${index} - Is first: ${isFirstItem}, Delay: ${ajaxDelay}ms`);
            
            setTimeout(function() {
                // Verify bahan hasn't changed
                const currentBahan = card.find('.bahan-select').val();
                console.log(`[HANDLE CHANGE] Item #${index} - After delay, current bahan: ${currentBahan}, expected: ${idBahan}`);
                
                if (currentBahan === idBahan) {
                    console.log(`[HANDLE CHANGE] Item #${index} - ===== EXECUTING FIFO LOAD =====`);
                    loadFifoBatches(index, idBahan);
                } else {
                    console.log(`[HANDLE CHANGE] Item #${index} - Bahan changed during delay, CANCELING`);
                    delete batchLoadQueue[index];
                    card.attr('data-batches-loading', 'false');
                }
            }, ajaxDelay);
            
        } else if (alreadyLoaded === 'true') {
            console.log(`[HANDLE CHANGE] Item #${index} - Batches already loaded, RECALCULATING`);
            calculateEstimatedTotalV2(index);
        } else if (isQueued) {
            console.log(`[HANDLE CHANGE] Item #${index} - Already queued, WAITING`);
        }
        
        console.log(`[HANDLE CHANGE] ===== Item #${index} END =====`);
    }
    
    function calculateSisa(index) {
        const card = $(`[data-index="${index}"]`);
        const bahanSelect = card.find('.bahan-select');
        const selectedOption = bahanSelect.find(':selected');
        const stok = parseFloat(selectedOption.data('stok')) || 0;
        const jumlah = parseFloat(card.find('.jumlah-input').val()) || 0;
        const satuan = selectedOption.data('satuan') || 'Unit';
        
        const sisa = stok - jumlah;
        card.find('.sisa-text').val(sisa.toLocaleString('id-ID') + ' ' + satuan);
        
        console.log(`[CALC SISA] Item #${index} - Stok: ${stok}, Jumlah: ${jumlah}, Sisa: ${sisa}`);
        
        // Validation styling
        if (jumlah > stok) {
            card.find('.sisa-text').addClass('is-invalid text-danger fw-bold');
            card.find('.jumlah-input').addClass('is-invalid');
        } else {
            card.find('.sisa-text').removeClass('is-invalid text-danger fw-bold');
            card.find('.jumlah-input').removeClass('is-invalid');
        }
        
        // CRITICAL: Tunggu batches selesai load dulu
        const batchesLoaded = card.attr('data-batches-loaded');
        const isLoading = card.attr('data-batches-loading');
        
        console.log(`[CALC SISA] Item #${index} - Batches loaded: ${batchesLoaded}, Loading: ${isLoading}`);
        
        if (batchesLoaded === 'true' && isLoading === 'false') {
            console.log(`[CALC SISA] Item #${index} - Calling calculateEstimatedTotalV2`);
            calculateEstimatedTotalV2(index);
        } else {
            console.log(`[CALC SISA] Item #${index} - Waiting for batches to load before calculating estimate`);
        }
    }
    
    function loadFifoBatches(index, idBahan) {
        const card = $(`[data-index="${index}"]`);
        const batchInfo = card.find('.fifo-batch-info');
        const batchList = card.find('.batch-list');
        
        console.log(`[LOAD FIFO] ===== Item #${index} START AJAX =====`);
        console.log(`[LOAD FIFO] Item #${index} - Fetching batches for bahan ID: ${idBahan}`);
        
        // Show loading state
        batchList.html('<div class="text-center py-2"><i class="fas fa-spinner fa-spin me-2"></i>Memuat batch FIFO...</div>');
        batchInfo.removeClass('d-none');
        
        // Cancel previous AJAX if exists
        if (ajaxControllers[index]) {
            console.log(`[LOAD FIFO] Item #${index} - Aborting previous AJAX`);
            ajaxControllers[index].abort();
        }
        
        ajaxControllers[index] = $.ajax({
            url: `/pemakaian-bahan-baku/get-fifo-batches/${idBahan}`,
            method: 'GET',
            timeout: 15000,
            success: function(response) {
                console.log(`[LOAD FIFO] ===== Item #${index} AJAX SUCCESS =====`);
                console.log(`[LOAD FIFO] Item #${index} - Response:`, response);
                
                // Cleanup
                delete batchLoadQueue[index];
                delete ajaxControllers[index];
                card.attr('data-batches-loading', 'false');
                
                if (response.success && response.batches && response.batches.length > 0) {
                    console.log(`[LOAD FIFO] Item #${index} - Found ${response.batches.length} batches`);
                    
                    // Build batch table
                    let html = '<div class="table-responsive mt-1"><table class="table table-sm table-bordered mb-0 small">';
                    html += '<thead><tr><th>Tanggal</th><th>Batch</th><th class="text-end">Sisa</th><th class="text-end">Harga</th></tr></thead><tbody>';
                    
                    response.batches.forEach((batch, idx) => {
                        console.log(`[LOAD FIFO] Item #${index} - Batch ${idx + 1}:`, batch);
                        html += `<tr>
                            <td>${batch.tanggal_masuk}</td>
                            <td><span class="badge bg-secondary">#${batch.id_stok}</span></td>
                            <td class="text-end">${parseFloat(batch.sisa_stok).toLocaleString('id-ID')}</td>
                            <td class="text-end">Rp ${parseFloat(batch.harga_per_satuan).toLocaleString('id-ID')}</td>
                        </tr>`;
                    });
                    
                    html += '</tbody></table></div>';
                    batchList.html(html);
                    
                    // Force show
                    batchInfo.removeClass('d-none');
                    
                    console.log(`[LOAD FIFO] Item #${index} - Table rendered, showing batch info`);
                    
                    // Store batches data - DEEP CLONE
                    const batchesCopy = JSON.parse(JSON.stringify(response.batches));
                    card.data('batches', batchesCopy);
                    card.attr('data-batches-loaded', 'true');
                    
                    console.log(`[LOAD FIFO] Item #${index} - Stored ${batchesCopy.length} batches in card data`);
                    console.log(`[LOAD FIFO] Item #${index} - Batches data:`, card.data('batches'));
                    
                    // Calculate estimated total - IMMEDIATE
                    const currentJumlah = parseFloat(card.find('.jumlah-input').val()) || 0;
                    console.log(`[LOAD FIFO] Item #${index} - Current jumlah value: ${currentJumlah}`);
                    
                    console.log(`[LOAD FIFO] Item #${index} - ===== TRIGGERING CALCULATION =====`);
                    
                    // FORCE calculate regardless of jumlah
                    setTimeout(function() {
                        console.log(`[LOAD FIFO] Item #${index} - Executing calculateEstimatedTotalV2`);
                        calculateEstimatedTotalV2(index);
                    }, 200);
                    
                } else {
                    console.log(`[LOAD FIFO] Item #${index} - No batches available in response`);
                    batchList.html('<div class="text-muted small">Tidak ada batch FIFO tersedia</div>');
                    batchInfo.removeClass('d-none');
                    card.data('batches', []);
                    card.attr('data-batches-loaded', 'true');
                    
                    // Reset estimasi box
                    card.attr('data-estimasi', '0');
                    card.find('.item-biaya-input').val('0');
                    updateGrandTotal();
                }
                
                console.log(`[LOAD FIFO] ===== Item #${index} AJAX END =====`);
            },
            error: function(xhr, status, error) {
                if (status === 'abort') {
                    console.log(`[LOAD FIFO] Item #${index} - AJAX ABORTED`);
                    return;
                }
                
                console.error(`[LOAD FIFO] ===== Item #${index} AJAX ERROR =====`);
                console.error(`[LOAD FIFO] Item #${index} - Status: ${status}, Error: ${error}`);
                
                // Cleanup
                delete batchLoadQueue[index];
                delete ajaxControllers[index];
                card.attr('data-batches-loading', 'false');
                
                batchList.html('<div class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i>Gagal memuat batch FIFO</div>');
                batchInfo.removeClass('d-none');
                card.data('batches', []);
                card.attr('data-batches-loaded', 'false');
            }
        });
    }
    
    // OLD calculateEstimatedTotal removed
    
    function updateGrandTotal() {
        let grandTotal = 0;
        
        $('.item-row').each(function() {
            const estimasi = parseFloat($(this).attr('data-estimasi')) || 0;
            grandTotal += estimasi;
        });
        
        console.log(`[GRAND TOTAL] Rp ${grandTotal.toLocaleString('id-ID')}`);
        
        $('#totalBiaya').text('Rp ' + grandTotal.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }));
    }
    
    function renumberItems() {
        $('.item-number').each(function(i) {
            $(this).text(i + 1);
        });
    }
    
    function updateSummary() {
        $('#totalItems').text(items.length);
        
        if (items.length > 0) {
            $('#summaryCard').show();
        } else {
            $('#summaryCard').hide();
        }
    }
</script>
@endsection