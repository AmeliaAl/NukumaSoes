<x-app-layout>
    <div class="py-8 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- ==================================
             KATEGORI PRODUK SECTION
        =================================== -->
        <div class="mb-12">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-4xl font-serif font-bold text-[#d4af37] drop-shadow-md">Kategori Produk</h1>
                    <p class="text-sm text-gray-300 mt-1">Kelola daftar kategori produk Anda</p>
                </div>
                <a href="{{ route('kategori.create') }}" 
                   class="inline-flex items-center px-6 py-2.5 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] border border-[#d4af37] rounded-xl font-bold text-sm text-[#d4af37] uppercase tracking-wider hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all shadow-[0_0_15px_rgba(212,175,55,0.3)]">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Kategori
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50/90 backdrop-blur-sm border-l-4 border-green-500 rounded-r-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Table Container -->
            <div class="rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.5)] overflow-hidden border-2 border-[#d4af37]/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y-2 divide-[#d4af37]/40">
                        <thead class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6]">
                            <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider w-20">Kategori ID</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20 whitespace-nowrap">Nama Kategori</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20 whitespace-nowrap">Berat (gr)</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20 whitespace-nowrap">Jenis Kemasan</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20 whitespace-nowrap">Masa Simpan</th>
                                    <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20 whitespace-nowrap">Unit Waktu</th>
                                    <th scope="col" class="px-6 py-4 text-right text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse($categories as $index => $category)
                                <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-8 h-8 rounded-full bg-[#7a0e14] border-2 border-[#d4af37] flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ $category->id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-800 font-bold border-l border-[#d4af37]/10">{{ $category->nama_kategori }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-800 border-l border-[#d4af37]/10">{{ $category->berat ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-800 border-l border-[#d4af37]/10">{{ $category->jenis_kemasan ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-800 border-l border-[#d4af37]/10">
                                        {{ $category->masa_simpan ?: '-' }} 
                                        {{ $category->masa_simpan ? ucfirst($category->satuan_masa_simpan) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-800 border-l border-[#d4af37]/10">
                                        {{ $category->satuan_masa_simpan ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium border-l border-[#d4af37]/10">
                                        <div class="flex justify-end items-center space-x-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('kategori.edit', $category) }}" class="text-[#b89553] hover:text-[#7a0e14] transition-colors" title="Edit">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('kategori.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[#b89553] hover:text-[#7a0e14] transition-colors" title="Hapus">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 italic bg-[#fdf9eb]">
                                        Belum ada kategori data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================================
             VARIAN RASA SECTION
        =================================== -->
        <div class="mt-16 mb-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h2 class="text-3xl font-serif font-bold text-[#d4af37] drop-shadow-md">Varian Rasa</h2>
                    <p class="text-sm text-gray-300 mt-1">Kelola daftar unik varian rasa produk</p>
                </div>
                <a href="{{ route('rasa.create') }}" 
                   class="inline-flex items-center px-6 py-2.5 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] border border-[#d4af37] rounded-xl font-bold text-sm text-[#d4af37] uppercase tracking-wider hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all shadow-[0_0_15px_rgba(212,175,55,0.3)]">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Rasa
                </a>
            </div>

            <!-- Table Container -->
            <div class="rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.5)] overflow-hidden border-2 border-[#d4af37]/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y-2 divide-[#d4af37]/40">
                        <thead class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6]">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider w-20">No</th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20">Nama Rasa</th>
                                <th scope="col" class="px-6 py-4 text-right text-sm font-black text-black uppercase tracking-wider border-l border-[#d4af37]/20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse($flavors as $index => $flavor)
                                <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-8 h-8 rounded-full bg-[#7a0e14] border-2 border-[#d4af37] flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-base text-gray-800 font-semibold border-l border-[#d4af37]/10">{{ $flavor->nama_rasa }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-base font-medium border-l border-[#d4af37]/10">
                                        <div class="flex justify-end items-center space-x-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('rasa.edit', $flavor) }}" class="text-[#b89553] hover:text-[#7a0e14] transition-colors" title="Edit">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('rasa.destroy', $flavor) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rasa ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[#b89553] hover:text-[#7a0e14] transition-colors" title="Hapus">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500 italic bg-[#fdf9eb]">
                                        Belum ada varian rasa yang terdaftar di produk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
