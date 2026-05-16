<x-app-layout>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="w-full max-w-4xl px-6 relative z-10">
            <!-- Glassmorphism Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[40px] shadow-[0_30px_60px_rgba(0,0,0,0.4)] p-10">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-serif font-black text-[#d4af37] drop-shadow-lg uppercase tracking-widest">Tambah Persediaan</h2>
                    <p class="text-white/60 text-xs font-bold mt-2 tracking-[0.2em]">PENCATATAN STOK MASUK & BATCH PRODUKSI</p>
                </div>

                <form method="POST" action="{{ route('inventory-entry.store') }}" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- PRODUK ID -->
                        <div>
                            <label for="kode_produk" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">PRODUK ID <span class="text-red-500 font-black">*</span></label>
                            <div class="relative group">
                                <select id="kode_produk" name="kode_produk" 
                                        class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('kode_produk') border-red-400 @enderror" 
                                        required autofocus>
                                    <option value="">Pilih Produk ID</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->kode_produk }}" 
                                                data-nama="{{ $product->nama_produk }}" 
                                                data-rasa="{{ $product->rasa_produk }}" 
                                                data-kategori="{{ $product->kategori }}"
                                                data-harga="{{ $product->harga_jual ?? $product->harga }}"
                                                {{ old('kode_produk') == $product->kode_produk ? 'selected' : '' }}>
                                            {{ $product->kode_produk }} - {{ $product->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('kode_produk')" class="mt-2" />
                        </div>

                        <!-- NO BATCH -->
                        <div>
                            <label for="no_batch" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">NO BATCH</label>
                            <input id="no_batch" type="text" name="no_batch" value="{{ old('no_batch') }}" 
                                   class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300" 
                                   placeholder="Input Manual No Batch" />
                            <x-input-error :messages="$errors->get('no_batch')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- VARIAN RASA -->
                        <div>
                            <label for="rasa_produk" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">VARIAN RASA</label>
                            <div class="relative">
                                <select id="rasa_produk" name="rasa_produk" 
                                        class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none">
                                    <option value="">Pilih Rasa (Opsional)</option>
                                    @foreach($flavors as $flavor)
                                        <option value="{{ $flavor->nama_rasa }}" {{ old('rasa_produk') == $flavor->nama_rasa ? 'selected' : '' }}>
                                            {{ $flavor->nama_rasa }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- KATEGORI -->
                        <div>
                            <label for="kategori" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">KATEGORI <span class="text-red-500 font-black">*</span></label>
                            <div class="relative">
                                <select id="kategori" name="kategori" 
                                        class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none" 
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->nama_kategori }}" 
                                                data-id="{{ $category->id }}"
                                                data-harga="{{ $category->harga }}" 
                                                data-masasimpan="{{ $category->masa_simpan }}" 
                                                data-satuan="{{ $category->satuan_masa_simpan }}"
                                                {{ old('kategori') == $category->nama_kategori ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- JUMLAH PER BATCH -->
                        <input type="hidden" name="jumlah" value="0">
                        <div>
                            <label for="jumlah_per_batch" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">JUMLAH PER BATCH <span class="text-red-500 font-black">*</span></label>
                            <div class="relative">
                                <input id="jumlah_per_batch" type="number" name="jumlah_per_batch" value="{{ old('jumlah_per_batch', 0) }}" 
                                       class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('jumlah_per_batch') border-red-400 @enderror" 
                                       min="0" required>
                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold">pack</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('jumlah_per_batch')" class="mt-2" />
                        </div>

                        <!-- STOK AWAL -->
                        <div>
                            <label for="stok_awal" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">STOK AWAL</label>
                            <div class="relative">
                                <input id="stok_awal" type="number" name="stok_awal" value="{{ old('stok_awal', 0) }}" 
                                       class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300" 
                                       min="0">
                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold">unit</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('stok_awal')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- HARGA PER PCS -->
                        <div class="md:col-span-2">
                            <label for="harga_jual" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">HARGA PER PCS <span class="text-red-500 font-black">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold">Rp</span>
                                </div>
                                <input id="harga_jual" type="number" name="harga_jual" value="{{ old('harga_jual', 0) }}" 
                                       class="w-full bg-white border-2 border-white/5 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-black text-xl focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" 
                                       required />
                            </div>
                        </div>

                        <!-- TOTAL -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">TOTAL ESTIMASI</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-gray-400 font-bold">Rp</span>
                                </div>
                                <input id="total_estimasi" type="text" readonly
                                       class="w-full bg-black/20 border-2 border-[#d4af37]/30 rounded-2xl pl-12 pr-5 py-4 text-[#d4af37] font-black text-2xl focus:ring-0 transition-all cursor-not-allowed" 
                                       value="0" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- PENGEMASAN -->
                        <div>
                            <label for="tgl_masuk" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">PENGEMASAN <span class="text-red-500 font-black">*</span></label>
                            <input id="tgl_masuk" type="date" name="tgl_masuk" value="{{ old('tgl_masuk', date('Y-m-d')) }}" 
                                   class="w-full bg-white border-2 border-white/5 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" 
                                   required />
                        </div>

                        <!-- MASA SIMPAN -->
                        <div>
                            <label class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">MASA SIMPAN</label>
                            <div class="flex items-center space-x-2 bg-white/20 rounded-2xl px-5 py-4 border-2 border-white/30 backdrop-blur-sm">
                                <input id="masa_simpan_display" class="bg-transparent border-none p-0 w-full text-white font-black text-xl focus:ring-0" type="text" readonly value="{{ old('masa_simpan', 0) }}" />
                                <span id="satuan_masa_simpan_display" class="text-[#d4af37] font-black text-[10px] uppercase tracking-widest">{{ old('satuan_masa_simpan', 'hari') }}</span>
                            </div>
                        </div>

                        <!-- ESTIMASI EXPIRED -->
                        <div>
                            <label class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">ESTIMASI EXPIRED</label>
                            <div class="bg-[#7a0e14]/40 rounded-2xl px-5 py-4 border-2 border-[#d4af37]/30 backdrop-blur-md flex items-center justify-center min-h-[62px]">
                                <input id="tgl_expired_display" class="bg-transparent border-none p-0 w-full text-[#f3d9a2] font-black text-lg text-center focus:ring-0" type="text" readonly value="{{ old('tgl_expired', '-') }}" />
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="nama_produk" id="nama_produk" value="{{ old('nama_produk') }}">
                    <input type="hidden" name="masa_simpan" id="masa_simpan" value="{{ old('masa_simpan', 0) }}">
                    <input type="hidden" name="satuan_masa_simpan" id="satuan_masa_simpan" value="{{ old('satuan_masa_simpan', 'hari') }}">
                    <input type="hidden" name="satuan" value="Pcs">
                    <input type="hidden" name="stok_minimum" value="0">

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-[#5a0f12] hover:bg-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#8b6e22] shadow-[0_10px_20px_rgba(90,15,18,0.3)] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[100px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Persediaan</span>
                        </button>
                        
                        <a href="{{ route('persediaan-produk.index') }}" 
                           class="flex-1 bg-[#5c677d] hover:bg-[#4a5568] text-white font-black py-6 rounded-2xl border-2 border-[#4a5568] shadow-[0_10px_20px_rgba(92,103,125,0.3)] transition-all flex items-center justify-center uppercase tracking-widest text-sm min-h-[100px]">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Master prices from controller
            const masterPrices = @json($prices);

            // Function to calculate and display total
            function calculateTotal() {
                const jumlahInput = document.getElementById('jumlah_per_batch');
                const hargaInput = document.getElementById('harga_jual');
                const totalDisplay = document.getElementById('total_estimasi');

                if (jumlahInput && hargaInput && totalDisplay) {
                    const jumlah = parseFloat(jumlahInput.value) || 0;
                    const harga = parseFloat(hargaInput.value) || 0;
                    const total = jumlah * harga;
                    
                    totalDisplay.value = total.toLocaleString('id-ID');
                }
            }

            // Function to calculate and display expiry date
            function calculateExpiry() {
                const tglMasukInput = document.getElementById('tgl_masuk');
                const masaSimpanInput = document.getElementById('masa_simpan');
                const satuanInput = document.getElementById('satuan_masa_simpan');
                const expiredDisplay = document.getElementById('tgl_expired_display');

                if (tglMasukInput && tglMasukInput.value && masaSimpanInput && masaSimpanInput.value) {
                    const date = new Date(tglMasukInput.value);
                    const value = parseInt(masaSimpanInput.value);
                    const unit = satuanInput.value;
                    
                    if (!isNaN(value)) {
                        if (unit === 'hari') {
                            date.setDate(date.getDate() + value);
                        } else if (unit === 'bulan') {
                            date.setMonth(date.getMonth() + value);
                        } else if (unit === 'tahun') {
                            date.setFullYear(date.getFullYear() + value);
                        }
                        
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        
                        expiredDisplay.value = `${day}/${month}/${year}`;
                    } else {
                        expiredDisplay.value = '-';
                    }
                } else if (expiredDisplay) {
                    expiredDisplay.value = '-';
                }
            }

            // Product selection trigger
            document.getElementById('kode_produk').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption || !selectedOption.value) return;

                const nama = selectedOption.getAttribute('data-nama');
                const rasa = selectedOption.getAttribute('data-rasa');
                const kategori = selectedOption.getAttribute('data-kategori');
                const harga = selectedOption.getAttribute('data-harga');

                // Set hidden product name
                document.getElementById('nama_produk').value = nama || '';
                
                // Set flavor
                const rasaSelect = document.getElementById('rasa_produk');
                if (rasaSelect && rasa) rasaSelect.value = rasa;
                
                // Set price directly from Product data as requested
                const hargaInput = document.getElementById('harga_jual');
                if (hargaInput && harga) {
                    hargaInput.value = harga;
                }

                // Set category and trigger automatic shelf-life fill (but we'll keep the price from product)
                const kategoriSelect = document.getElementById('kategori');
                if (kategoriSelect && kategori) {
                    kategoriSelect.value = kategori;
                    // Trigger change event to fetch data from the selected category
                    // We need to be careful not to let category overwrite the product price
                    kategoriSelect.dispatchEvent(new Event('change'));
                }
            });

            // Category selection trigger (Auto-fill Price and Shelf Life)
            document.getElementById('kategori').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    document.getElementById('harga_jual').value = 0;
                    document.getElementById('masa_simpan').value = 0;
                    document.getElementById('masa_simpan_display').value = 0;
                    return;
                }

                const categoryId = selectedOption.getAttribute('data-id'); 
                const fallbackHarga = selectedOption.getAttribute('data-harga');
                const masaSimpan = selectedOption.getAttribute('data-masasimpan');
                const satuan = selectedOption.getAttribute('data-satuan');

                // 1. AUTO-FILL HARGA PER PCS (Taken from Harga Produk list - Default to 'Umum')
                const hargaInput = document.getElementById('harga_jual');
                
                if (masterPrices && categoryId) {
                    // Search in Master Data Harga based on Category
                    const categoryPrices = masterPrices.filter(p => p.kategori_id == categoryId);
                    if (categoryPrices.length > 0) {
                        // Find 'Umum' price or just the first available price for this category
                        const targetPrice = categoryPrices.find(p => p.jenis_mitra === 'Umum') || 
                                           categoryPrices[0];
                        hargaInput.value = targetPrice.harga;
                    } else if (fallbackHarga) {
                        hargaInput.value = fallbackHarga;
                    }
                } else if (fallbackHarga) {
                    hargaInput.value = fallbackHarga;
                }

                // 2. AUTO-FILL MASA SIMPAN
                let finalMasaSimpan = masaSimpan || 0;
                let finalSatuan = satuan || 'hari';

                const lowerKategori = selectedOption.value.toLowerCase();
                if (lowerKategori.includes('toples')) {
                    finalMasaSimpan = 6;
                    finalSatuan = 'bulan';
                } else if (lowerKategori.includes('pouch') || lowerKategori.includes('plastik')) {
                    finalMasaSimpan = 10;
                    finalSatuan = 'bulan';
                }

                document.getElementById('masa_simpan').value = finalMasaSimpan;
                document.getElementById('masa_simpan_display').value = finalMasaSimpan;
                document.getElementById('satuan_masa_simpan').value = finalSatuan;
                
                const satuanDisplay = document.getElementById('satuan_masa_simpan_display');
                if (satuanDisplay) {
                    satuanDisplay.innerText = finalSatuan.toUpperCase();
                }
                
                // Recalculate Expiry and Total
                calculateExpiry();
                calculateTotal();
            });

            // Recalculate on input change
            document.getElementById('jumlah_per_batch').addEventListener('input', calculateTotal);
            document.getElementById('harga_jual').addEventListener('input', calculateTotal);

            // Jenis Mitra change trigger removed as field is removed

            // Recalculate on date change
            document.getElementById('tgl_masuk').addEventListener('change', calculateExpiry);

            // Initial calculation on page load
            window.addEventListener('DOMContentLoaded', () => {
                const kategoriSelect = document.getElementById('kategori');
                if (kategoriSelect && kategoriSelect.value) {
                    kategoriSelect.dispatchEvent(new Event('change'));
                }
                calculateExpiry();
                calculateTotal();
            });
        </script>
    </div>
</x-app-layout>
