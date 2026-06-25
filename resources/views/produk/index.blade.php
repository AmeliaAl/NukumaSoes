<x-app-layout>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #fff9eb;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #d4af37 0%, #b8860b 100%);
            border-radius: 10px;
            border: 2px solid #fff9eb;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #8b6e22;
        }
    </style>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
                <div>
                    <h1 class="text-4xl font-serif font-bold text-[#d4af37] drop-shadow-md">Daftar Produk</h1>
                    <p class="text-sm text-gray-300 mt-1 uppercase tracking-wider font-medium">Manajemen Master Data Produk & Varian</p>
                </div>

                <div class="flex flex-wrap items-center justify-center md:justify-end gap-4">
                    <!-- Modern Filter Form -->
                    <form action="{{ route('produk.index') }}" method="GET" class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-1.5 rounded-xl border border-white/20 shadow-xl">
                        <select name="kategori" onchange="this.form.submit()" class="bg-transparent border-none text-white font-bold text-sm focus:ring-0 cursor-pointer min-w-[140px]">
                            <option value="" class="bg-[#2d1b15]">Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->nama_kategori }}" {{ request('kategori') == $category->nama_kategori ? 'selected' : '' }} class="bg-[#2d1b15]">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                        <div class="h-6 w-px bg-white/20"></div>
                        <select name="rasa" onchange="this.form.submit()" class="bg-transparent border-none text-white font-bold text-sm focus:ring-0 cursor-pointer min-w-[140px]">
                            <option value="" class="bg-[#2d1b15]">Rasa</option>
                            @foreach($flavors as $flavor)
                                <option value="{{ $flavor->nama_rasa }}" {{ request('rasa') == $flavor->nama_rasa ? 'selected' : '' }} class="bg-[#2d1b15]">{{ $flavor->nama_rasa }}</option>
                            @endforeach
                        </select>
                    </form>

                    <a href="{{ route('produk.create') }}" 
                       class="inline-flex items-center px-6 py-2.5 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] border border-[#d4af37] rounded-xl font-bold text-sm text-[#d4af37] uppercase tracking-wider hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all shadow-[0_0_15px_rgba(212,175,55,0.3)]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Produk
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-8 p-4 bg-green-500/10 backdrop-blur-xl border-l-4 border-green-500 rounded-r-2xl flex items-center gap-3 animate-fadeIn">
                    <div class="bg-green-500 rounded-full p-1 shadow-lg shadow-green-500/30">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Premium Table Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-[40px] border-2 border-[#d4af37]/60 shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden">
                <div class="overflow-x-auto overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <table class="w-full min-w-[1000px] border-collapse relative">
                        <thead class="sticky top-0 z-20 shadow-sm">
                            <tr class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6] border-b-2 border-[#d4af37]/20">
                                <th class="px-6 py-6 text-left text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">ID</th>
                                <th class="px-6 py-6 text-left text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Nama Produk</th>
                                <th class="px-6 py-6 text-left text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Rasa</th>
                                <th class="px-6 py-6 text-center text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Satuan</th>
                                <th class="px-6 py-6 text-center text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Kategori</th>
                                <th class="px-6 py-6 text-center text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Harga</th>
                                <th class="px-6 py-6 text-right text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">BBB</th>
                                <th class="px-6 py-6 text-right text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">BTKL</th>
                                <th class="px-6 py-6 text-right text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">BOP</th>
                                <th class="px-6 py-6 text-right text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">HPP</th>
                                <th class="px-6 py-6 text-center text-sm font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Jenis Produk</th>
                                <th class="px-6 py-6 text-center text-sm font-black text-black uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse($products as $index => $product)
                                <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                    <td class="px-6 py-5 text-center border-r border-[#d4af37]/10">
                                        <div class="w-10 h-10 rounded-full bg-[#7a0e14] flex items-center justify-center text-white font-bold text-sm shadow-md mx-auto border-2 border-[#d4af37]/30">
                                            {{ $product->kode_produk }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 border-r border-[#d4af37]/10">
                                        <div class="flex flex-col">
                                            <span class="text-base font-bold text-gray-800 group-hover:text-[#7a0e14] transition-colors uppercase tracking-tight">{{ $product->nama_produk }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-700 font-medium border-r border-[#d4af37]/10 italic">{{ $product->rasa_produk ?: '-' }}</td>
                                    <td class="px-6 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-center">
                                        <span class="px-4 py-1 bg-white/50 text-[#7a0e14] rounded-full border border-[#d4af37]/20 text-xs uppercase font-black">{{ $product->satuan ?: '-' }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-base text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center">
                                        <span class="block whitespace-nowrap mx-auto uppercase tracking-tighter" title="{{ $product->kategori }}">{{ $product->kategori }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right whitespace-nowrap">
                                        Rp {{ number_format($product->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right whitespace-nowrap">
                                        Rp {{ number_format($product->bbb ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right whitespace-nowrap">
                                        Rp {{ number_format($product->btkl ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right whitespace-nowrap">
                                        Rp {{ number_format($product->bop ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right whitespace-nowrap">
                                        Rp {{ number_format($product->hpp ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-center border-r border-[#d4af37]/10">
                                        @php $jenisProduk = $product->jenis_produk ?? 'Brand Sendiri'; @endphp
                                        <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase inline-block {{ $jenisProduk === 'Maklon' ? 'bg-[#7a0e14]/10 text-[#7a0e14] border border-[#7a0e14]/20' : 'bg-green-500/10 text-green-700 border border-green-500/20' }}">
                                            {{ $jenisProduk }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-4">
                                            <a href="{{ route('produk.edit', $product->id) }}" class="p-2 rounded-xl text-[#b89553] hover:bg-[#d4af37] hover:text-white transition-all transform hover:scale-110 shadow-sm" title="Edit">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path></svg>
                                            </a>
                                            <form action="{{ route('produk.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-xl text-[#ff6b6b] hover:bg-[#ff6b6b] hover:text-white transition-all transform hover:scale-110 shadow-sm" title="Hapus">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p class="text-gray-400 italic font-medium">Belum ada produk yang ditambahkan dalam sistem.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Bottom Helper Text -->
            <div class="mt-8 flex justify-center">
                <div class="bg-black/5 backdrop-blur-sm px-6 py-2 rounded-full border border-black/10 shadow-sm">
                    <p class="text-[10px] font-black text-[#5a0f12] uppercase tracking-[0.3em]">Menampilkan total {{ $products->count() }} varian produk master</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
