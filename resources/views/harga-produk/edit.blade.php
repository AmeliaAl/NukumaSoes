    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative flex items-center justify-center">
        <div class="absolute inset-0 bg-black/20 pointer-events-none"></div>
        
        <div class="w-full max-w-2xl px-6 relative z-10">
            <!-- Glassmorphism Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[40px] shadow-[0_20px_50px_rgba(0,0,0,0.3)] p-10">
                <form method="POST" action="{{ route('harga-produk.update', $hargaProduk) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Kategori ID -->
                        <div>
                            <label for="kategori_id" class="block text-sm font-bold text-[#d4af37] mb-3">Kategori ID <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <select name="kategori_id" id="kategori_id" 
                                        class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-medium focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('kategori_id') border-red-400 @enderror" 
                                        required>
                                    <option value="">Pilih Kategori ID</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" data-nama="{{ $category->nama_kategori }}" {{ old('kategori_id', $hargaProduk->kategori_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->id }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('kategori_id')" class="mt-2" />
                        </div>

                        <!-- Nama Kategori -->
                        <div>
                            <label for="nama_kategori_display" class="block text-sm font-bold text-[#d4af37] mb-3">Nama Kategori</label>
                            <input type="text" id="nama_kategori_display" 
                                   class="w-full bg-white/80 border-2 border-white/30 rounded-2xl px-5 py-4 text-gray-400 font-medium placeholder-gray-400 shadow-inner leading-normal" 
                                   readonly value="{{ $hargaProduk->category ? $hargaProduk->category->nama_kategori : '' }}">
                        </div>
                    </div>

                    <!-- Jenis Mitra -->
                    <div>
                        <label for="jenis_mitra" class="block text-sm font-bold text-[#d4af37] mb-3">Jenis Mitra <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="jenis_mitra" id="jenis_mitra" 
                                    class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-medium focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('jenis_mitra') border-red-400 @enderror" 
                                    required>
                                <option value="">Pilih Jenis Mitra</option>
                                <option value="Agen" {{ old('jenis_mitra', $hargaProduk->jenis_mitra) == 'Agen' ? 'selected' : '' }}>Agen</option>
                                <option value="Riseller" {{ old('jenis_mitra', $hargaProduk->jenis_mitra) == 'Riseller' ? 'selected' : '' }}>Riseller</option>
                                <option value="Konsinyasi" {{ old('jenis_mitra', $hargaProduk->jenis_mitra) == 'Konsinyasi' ? 'selected' : '' }}>Konsinyasi</option>
                                <option value="Umum" {{ old('jenis_mitra', $hargaProduk->jenis_mitra) == 'Umum' ? 'selected' : '' }}>Umum</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_mitra')" class="mt-2" />
                    </div>

                    <!-- Harga -->
                    <div>
                        <label for="harga" class="block text-sm font-bold text-[#d4af37] mb-3">Harga <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <span class="text-gray-400 font-medium">Rp</span>
                            </div>
                            <input type="number" name="harga" id="harga" value="{{ old('harga', $hargaProduk->harga) }}" step="0.01" min="0"
                                   class="w-full bg-white border-2 border-white/50 rounded-2xl pl-14 pr-5 py-4 text-gray-700 font-medium focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 @error('harga') border-red-400 @enderror" 
                                   placeholder="0" required>
                        </div>
                        <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-[#5a0f12] hover:bg-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#8b6e22] shadow-[0_10px_20px_rgba(90,15,18,0.3)] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[100px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Perbarui Harga Produk</span>
                        </button>
                        
                        <a href="{{ route('harga-produk.index') }}" 
                           class="flex-1 bg-[#5c677d] hover:bg-[#4a5568] text-white font-black py-6 rounded-2xl border-2 border-[#4a5568] shadow-[0_10px_20px_rgba(92,103,125,0.3)] transition-all flex items-center justify-center uppercase tracking-widest text-sm min-h-[100px]">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.getElementById('kategori_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const nama = selectedOption.getAttribute('data-nama');
                const display = document.getElementById('nama_kategori_display');
                display.value = nama || '';
                if (nama) {
                    display.classList.remove('text-gray-400');
                    display.classList.add('text-gray-700');
                } else {
                    display.classList.remove('text-gray-700');
                    display.classList.add('text-gray-400');
                }
            });
            
            window.addEventListener('load', function() {
                const select = document.getElementById('kategori_id');
                if(select.value) {
                    select.dispatchEvent(new Event('change'));
                }
            });
        </script>
    </div>

