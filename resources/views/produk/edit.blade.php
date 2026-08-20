<x-app-layout>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="w-full max-w-4xl px-6 relative z-10">
            <!-- Glassmorphism Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[40px] shadow-[0_30px_60px_rgba(0,0,0,0.4)] p-10">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-serif font-black text-[#d4af37] drop-shadow-lg uppercase tracking-widest">Edit Produk</h2>
                    <p class="text-white/60 text-xs font-bold mt-2 tracking-[0.2em]">PERBARUI MASTER DATA PRODUK & VARIAN</p>
                </div>

                <form method="POST" action="{{ route('produk.update', $product) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- PRODUK ID -->
                        <div>
                            <label for="kode_produk" class="block text-sm font-bold text-[#d4af37] mb-3">PRODUK ID <span class="text-red-500 font-black">*</span></label>
                            <input type="text" name="kode_produk" id="kode_produk" value="{{ old('kode_produk', $product->kode_produk) }}" 
                                   class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all uppercase placeholder-gray-300 @error('kode_produk') border-red-400 @enderror" 
                                   placeholder="CONTOH: PRD-001" required autofocus>
                            <x-input-error :messages="$errors->get('kode_produk')" class="mt-2" />
                        </div>

                        <!-- NAMA PRODUK -->
                        <div>
                            <label for="nama_produk" class="block text-sm font-bold text-[#d4af37] mb-3">NAMA PRODUK <span class="text-red-500 font-black">*</span></label>
                            <input type="text" name="nama_produk" id="nama_produk" value="{{ old('nama_produk', $product->nama_produk) }}" 
                                   class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('nama_produk') border-red-400 @enderror" 
                                   placeholder="NAMA PRODUK ANDA" required>
                            <x-input-error :messages="$errors->get('nama_produk')" class="mt-2" />
                        </div>

                        <!-- RASA -->
                        <div>
                            <label for="rasa_produk" class="block text-sm font-bold text-[#d4af37] mb-3">VARIAN RASA</label>
                            <div class="relative">
                                <select name="rasa_produk" id="rasa_produk" 
                                        class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('rasa_produk') border-red-400 @enderror">
                                    <option value="">Pilih Rasa (Opsional)</option>
                                    @foreach($flavors as $flavor)
                                        <option value="{{ $flavor->nama_rasa }}" {{ old('rasa_produk', $product->rasa_produk) == $flavor->nama_rasa ? 'selected' : '' }}>
                                            {{ $flavor->nama_rasa }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('rasa_produk')" class="mt-2" />
                        </div>

                        <!-- SATUAN -->
                        <div>
                            <label for="satuan" class="block text-sm font-bold text-[#d4af37] mb-3">SATUAN <span class="text-red-500 font-black">*</span></label>
                            <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $product->satuan) }}" 
                                   class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('satuan') border-red-400 @enderror" 
                                   required>
                            <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                        </div>
                    </div>

                    <!-- KATEGORI -->
                    <div>
                        <label for="kategori" class="block text-sm font-bold text-[#d4af37] mb-3">KATEGORI PRODUK <span class="text-red-500 font-black">*</span></label>
                        <div class="relative">
                            <select name="kategori" id="kategori" 
                                    class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('kategori') border-red-400 @enderror" 
                                    required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->nama_kategori }}" data-harga="{{ $category->harga }}" data-masasimpan="{{ $category->masa_simpan }}" {{ old('kategori', $product->kategori) == $category->nama_kategori ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
                    </div>

                    <!-- HARGA -->
                    <div>
                        <label for="harga" class="block text-sm font-bold text-[#d4af37] mb-3">HARGA PRODUK <span class="text-red-500 font-black">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                                <span class="text-gray-500 font-bold">Rp</span>
                            </div>
                            <input type="number" name="harga" id="harga" value="{{ old('harga', $product->harga) }}" 
                                   class="w-full bg-white border-2 border-white/50 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('harga') border-red-400 @enderror" 
                                   placeholder="0" required min="0">
                        </div>
                        <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                    </div>

                    <!-- JENIS PRODUK -->
                    <div>
                        <label for="jenis_produk" class="block text-sm font-bold text-[#d4af37] mb-3">JENIS PRODUK <span class="text-red-500 font-black">*</span></label>
                        <div class="relative">
                            <select name="jenis_produk" id="jenis_produk"
                                    class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('jenis_produk') border-red-400 @enderror"
                                    required>
                                <option value="Brand Sendiri" {{ old('jenis_produk', $product->jenis_produk ?? 'Brand Sendiri') == 'Brand Sendiri' ? 'selected' : '' }}>Brand Sendiri</option>
                                <option value="Maklon" {{ old('jenis_produk', $product->jenis_produk ?? 'Brand Sendiri') == 'Maklon' ? 'selected' : '' }}>Maklon</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_produk')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- BIAYA BAHAN BAKU (BBB) -->
                        <div>
                            <label for="bbb" class="block text-sm font-bold text-[#d4af37] mb-3">BIAYA BAHAN BAKU (BBB)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="bbb" id="bbb" value="{{ old('bbb', $product->bbb) }}" 
                                       class="w-full bg-white border-2 border-white/50 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('bbb') border-red-400 @enderror" 
                                       placeholder="0" min="0" step="0.01">
                            </div>
                            <x-input-error :messages="$errors->get('bbb')" class="mt-2" />
                        </div>

                        <!-- BIAYA TENAGA KERJA LANGSUNG (BTKL) -->
                        <div>
                            <label for="btkl" class="block text-sm font-bold text-[#d4af37] mb-3">BIAYA TENAGA KERJA LANGSUNG (BTKL)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="btkl" id="btkl" value="{{ old('btkl', $product->btkl) }}" 
                                       class="w-full bg-white border-2 border-white/50 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('btkl') border-red-400 @enderror" 
                                       placeholder="0" min="0" step="0.01">
                            </div>
                            <x-input-error :messages="$errors->get('btkl')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- BIAYA OVERHEAD PABRIK (BOP) -->
                        <div>
                            <label for="bop" class="block text-sm font-bold text-[#d4af37] mb-3">BIAYA OVERHEAD PABRIK (BOP)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="bop" id="bop" value="{{ old('bop', $product->bop) }}" 
                                       class="w-full bg-white border-2 border-white/50 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('bop') border-red-400 @enderror" 
                                       placeholder="0" min="0" step="0.01">
                            </div>
                            <x-input-error :messages="$errors->get('bop')" class="mt-2" />
                        </div>

                        <!-- HP Produksi -->
                        <div>
                            <label for="hpp" class="block text-sm font-bold text-[#d4af37] mb-3">HP Produksi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="hpp" id="hpp" value="{{ old('hpp', $product->hpp) }}" 
                                       class="w-full bg-white border-2 border-white/50 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('hpp') border-red-400 @enderror" 
                                       placeholder="0" min="0" step="0.01">
                            </div>
                            <x-input-error :messages="$errors->get('hpp')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', $product->harga_jual) }}">
                    <input type="hidden" name="masa_simpan" id="masa_simpan" value="{{ old('masa_simpan', $product->masa_simpan) }}">
                    <input type="hidden" name="satuan_masa_simpan" value="{{ $product->satuan_masa_simpan }}">

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-[#5a0f12] hover:bg-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#8b6e22] shadow-[0_10px_20px_rgba(90,15,18,0.3)] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[100px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Perbarui Produk</span>
                        </button>
                        
                        <a href="{{ route('produk.index') }}" 
                           class="flex-1 bg-[#5c677d] hover:bg-[#4a5568] text-white font-black py-6 rounded-2xl border-2 border-[#4a5568] shadow-[0_10px_20px_rgba(92,103,125,0.3)] transition-all flex items-center justify-center uppercase tracking-widest text-sm min-h-[100px]">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.getElementById('kategori').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const harga = selectedOption.getAttribute('data-harga');
                const masaSimpan = selectedOption.getAttribute('data-masasimpan');
                if (harga) {
                    document.getElementById('harga_jual').value = harga;
                }
                if (masaSimpan) {
                    document.getElementById('masa_simpan').value = masaSimpan;
                }
            });
        </script>
    </div>
</x-app-layout>
