<x-app-layout>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="w-full max-w-4xl px-6 relative z-10">
            <!-- Glassmorphism Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[40px] shadow-[0_30px_60px_rgba(0,0,0,0.4)] p-10">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-serif font-black text-[#d4af37] drop-shadow-lg uppercase tracking-widest">Edit Kategori</h2>
                    <p class="text-white/60 text-xs font-bold mt-2 tracking-[0.2em]">PERBARUI MASTER DATA KATEGORI PRODUK</p>
                </div>

                <form action="{{ route('kategori.update', $kategori) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="group">
                        <label for="nama_kategori" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">
                            Nama Kategori <span class="text-red-500 font-black">*</span>
                        </label>
                        <input type="text" name="nama_kategori" id="nama_kategori" 
                            class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 shadow-sm" 
                            value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                            required>
                        @error('nama_kategori')
                            <p class="mt-2 text-sm text-red-400 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="group">
                            <label for="berat" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">
                                Berat (gr)
                            </label>
                            <input type="text" name="berat" id="berat" 
                                class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 shadow-sm" 
                                value="{{ old('berat', $kategori->berat) }}">
                            @error('berat')
                                <p class="mt-2 text-sm text-red-400 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label for="jenis_kemasan" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">
                                Kemasan
                            </label>
                            <input type="text" name="jenis_kemasan" id="jenis_kemasan" 
                                class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 shadow-sm" 
                                value="{{ old('jenis_kemasan', $kategori->jenis_kemasan) }}">
                            @error('jenis_kemasan')
                                <p class="mt-2 text-sm text-red-400 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label for="masa_simpan" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">
                                Masa Simpan
                            </label>
                            <input type="number" name="masa_simpan" id="masa_simpan" 
                                class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 shadow-sm" 
                                value="{{ old('masa_simpan', $kategori->masa_simpan) }}"
                                min="0">
                            @error('masa_simpan')
                                <p class="mt-2 text-sm text-red-400 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label for="satuan_masa_simpan" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">
                                Unit Waktu
                            </label>
                            <div class="relative">
                                <select name="satuan_masa_simpan" id="satuan_masa_simpan" 
                                    class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none shadow-sm">
                                    <option value="hari" {{ old('satuan_masa_simpan', $kategori->satuan_masa_simpan) == 'hari' ? 'selected' : '' }}>Hari</option>
                                    <option value="bulan" {{ old('satuan_masa_simpan', $kategori->satuan_masa_simpan) == 'bulan' ? 'selected' : '' }}>Bulan</option>
                                    <option value="tahun" {{ old('satuan_masa_simpan', $kategori->satuan_masa_simpan) == 'tahun' ? 'selected' : '' }}>Tahun</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            @error('satuan_masa_simpan')
                                <p class="mt-2 text-sm text-red-400 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-[#5a0f12] hover:bg-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#8b6e22] shadow-[0_10px_20px_rgba(90,15,18,0.3)] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[100px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Perbarui Kategori</span>
                        </button>
                        
                        <a href="{{ route('kategori.index') }}" 
                           class="flex-1 bg-[#5c677d] hover:bg-[#4a5568] text-white font-black py-6 rounded-2xl border-2 border-[#4a5568] shadow-[0_10px_20px_rgba(92,103,125,0.3)] transition-all flex items-center justify-center uppercase tracking-widest text-sm min-h-[100px]">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
