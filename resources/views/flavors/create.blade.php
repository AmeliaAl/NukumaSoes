<x-app-layout>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="w-full max-w-4xl px-6 relative z-10">
            <!-- Glassmorphism Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-[40px] shadow-[0_30px_60px_rgba(0,0,0,0.4)] p-10">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-serif font-black text-[#d4af37] drop-shadow-lg uppercase tracking-widest">Tambah Varian Rasa</h2>
                    <p class="text-white/60 text-xs font-bold mt-2 tracking-[0.2em]">PENGELOMPOKKAN VARIAN RASA PRODUK</p>
                </div>

                <form action="{{ route('rasa.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="group">
                        <label for="nama_rasa" class="block text-sm font-bold text-[#d4af37] mb-3 uppercase tracking-wider">
                            Nama Rasa <span class="text-red-500 font-black">*</span>
                        </label>
                        <input type="text" name="nama_rasa" id="nama_rasa" value="{{ old('nama_rasa') }}"
                            class="w-full bg-white border-2 border-white/50 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-300 shadow-sm" 
                            placeholder="Contoh: Manis, Coklat, Keju..."
                            required>
                        @error('nama_rasa')
                            <p class="mt-2 text-sm text-red-400 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-[#5a0f12] hover:bg-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#8b6e22] shadow-[0_10px_20px_rgba(90,15,18,0.3)] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[100px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Rasa</span>
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
