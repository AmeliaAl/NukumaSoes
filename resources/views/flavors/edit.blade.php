<x-app-layout>
    <div class="py-8 w-full">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('kategori.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke Daftar Kategori
                </a>
                <h1 class="text-3xl font-serif font-bold text-gray-900 mt-4">Edit Varian Rasa</h1>
                <p class="text-sm text-gray-500 mt-1">Ubah nama varian rasa</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <form action="{{ route('rasa.update', $rasa) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="nama_rasa" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Nama Rasa</label>
                        <input type="text" name="nama_rasa" id="nama_rasa" value="{{ old('nama_rasa', $rasa->nama_rasa) }}" 
                            class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring focus:ring-red-200 transition-all shadow-sm"
                            placeholder="Contoh: Manis, Coklat, Keju..." required>
                        @error('nama_rasa')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-red-800 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-900 active:bg-red-950 transition ease-in-out duration-150 shadow-lg shadow-red-900/20">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
