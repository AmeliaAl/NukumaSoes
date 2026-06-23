<x-app-layout>
    <x-slot name="header">
        Persediaan Produk
    </x-slot>

    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
                <div>
                    <h1 class="text-4xl font-serif font-bold text-[#d4af37] drop-shadow-md">Detail Persediaan Produk</h1>
                    <p class="text-sm text-gray-300 mt-1 uppercase tracking-wider font-medium">Monitoring Stok Berdasarkan Batch & Tanggal Expired</p>
                </div>

                <div class="flex flex-wrap items-center justify-center md:justify-end gap-4">
                    <!-- Modern Filter Form -->
                    <form action="{{ route('persediaan-produk.index') }}" method="GET" class="flex items-center gap-3 bg-white/10 backdrop-blur-md p-1.5 rounded-xl border border-white/20 shadow-xl">
                        <select name="kategori" onchange="this.form.submit()" class="bg-transparent border-none text-white font-bold text-sm focus:ring-0 cursor-pointer min-w-[160px]">
                            <option value="" class="bg-[#2d1b15]">Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->nama_kategori }}" {{ request('kategori') == $category->nama_kategori ? 'selected' : '' }} class="bg-[#2d1b15]">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </form>

                    <a href="{{ route('inventory-entry.create') }}" 
                       class="inline-flex items-center px-6 py-2.5 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] border border-[#d4af37] rounded-xl font-bold text-sm text-[#d4af37] uppercase tracking-wider hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all shadow-[0_0_15px_rgba(212,175,55,0.3)]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Persediaan
                    </a>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white/95 backdrop-blur-md rounded-[40px] border-2 border-[#d4af37]/60 shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden">
                <div class="overflow-x-auto overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <table class="w-full min-w-[1400px] border-collapse relative">
                        <thead class="sticky top-0 z-20 shadow-sm">
                            <tr class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6] border-b-2 border-[#d4af37]/20">
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">ID</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">BATCH</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">PRODUK</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">RASA</th>
                                <th class="px-4 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KATEGORI</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STOK AWAL</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">JUMLAH PER BATCH</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STOK SAAT INI</th>
                                <th class="px-4 py-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">HARGA</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KEMAS</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">EXP</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STATUS</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">SISA</th>
                                <th class="px-4 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse($inventories as $index => $inventory)
                                <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                    <td class="px-4 py-5 text-center border-r border-[#d4af37]/10">
                                        <div class="w-10 h-10 rounded-full bg-[#7a0e14] flex items-center justify-center text-white font-bold text-xs shadow-md mx-auto border-2 border-[#d4af37]/30">
                                            {{ $inventory->kode_produk }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 uppercase tracking-tight">{{ $inventory->no_batch ?? '-' }}</td>
                                    <td class="px-4 py-5 border-r border-[#d4af37]/10">
                                        <span class="text-base font-bold text-gray-900 group-hover:text-[#7a0e14] transition-colors line-clamp-1 uppercase tracking-tight">{{ $inventory->nama_produk }}</span>
                                    </td>
                                    <td class="px-4 py-5 text-base text-gray-600 border-r border-[#d4af37]/10 italic font-medium">{{ $inventory->rasa_produk ?? '-' }}</td>
                                    <td class="px-4 py-5 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 truncate max-w-[120px] uppercase tracking-tighter">{{ $inventory->kategori }}</td>
                                    <td class="px-4 py-5 text-base text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center bg-[#d4af37]/10 group-hover:bg-[#ffeec2]">
                                        {{ $inventory->stok_awal ?? 0 }} pack
                                    </td>
                                    <td class="px-4 py-5 text-base text-black font-black border-r border-[#d4af37]/10 text-center">{{ $inventory->jumlah_per_batch }} pack</td>
                                    <td class="px-4 py-5 text-base text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center bg-[#d4af37]/10">
                                        {{ $inventory->jumlah }} pack
                                    </td>
                                    <td class="px-4 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 text-right font-mono tracking-tighter">{{ number_format($inventory->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-700 border-r border-[#d4af37]/10 text-center font-bold">{{ $inventory->tgl_masuk ? \Carbon\Carbon::parse($inventory->tgl_masuk)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-5 text-sm text-black border-r border-[#d4af37]/10 text-center font-black">{{ $inventory->tgl_expired ? \Carbon\Carbon::parse($inventory->tgl_expired)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-5 text-center border-r border-[#d4af37]/10">
                                        @php
                                            $expiredDate = \Carbon\Carbon::parse($inventory->tgl_expired);
                                            $oneMonthFromNow = \Carbon\Carbon::now()->addMonth();
                                            $now = \Carbon\Carbon::now();
                                        @endphp
                                        @if($expiredDate->gt($oneMonthFromNow))
                                            <div class="px-3 py-1 rounded-full bg-green-500 text-white text-[10px] font-black uppercase shadow-sm">Aman</div>
                                        @elseif($expiredDate->isAfter($now->endOfDay()))
                                            <div class="px-3 py-1 rounded-full bg-orange-500 text-white text-[10px] font-black uppercase shadow-sm animate-pulse">Hampir Expired</div>
                                        @else
                                            <div class="px-3 py-1 rounded-full bg-red-600 text-white text-[10px] font-black uppercase shadow-sm">Expired</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-center">
                                        {{ $inventory->sisa_hari }} Hr
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('inventory-entry.edit', $inventory) }}" class="p-2 rounded-xl text-[#b89553] hover:bg-[#d4af37] hover:text-white transition-all transform hover:scale-110" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path></svg>
                                            </a>
                                            <form method="POST" action="{{ route('inventory-entry.destroy', $inventory) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-xl text-[#ff6b6b] hover:bg-[#ff6b6b] hover:text-white transition-all transform hover:scale-110" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus persediaan ini?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p class="text-gray-400 italic font-medium">Data persediaan produk belum tersedia.</p>
                                        </div>
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
