<x-app-layout>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6">
                <div class="text-center md:text-left">
                    <h1 class="text-4xl font-serif font-bold text-[#d4af37] drop-shadow-md">Produk Expired</h1>
                    <p class="text-sm text-gray-300 mt-2 uppercase tracking-widest font-medium">Manajemen Arus Kas & Biaya Operasional</p>
                </div>

                <div class="flex flex-wrap items-center justify-center md:justify-end gap-4">
                </div>
            </div>

            @if(session('success'))
                <div class="mb-8 bg-green-500/20 backdrop-blur-md border border-green-500/50 text-green-100 font-bold px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- EXPIRED HISTORY TABLE SECTION -->
            <div class="bg-white/95 backdrop-blur-md rounded-[40px] border-2 border-[#d4af37]/60 shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden mt-12 mb-12">
                <div class="p-8 pb-4 text-center">
                    <h2 class="text-3xl font-serif font-black text-[#7a0e14] drop-shadow-sm uppercase tracking-widest">Riwayat Produk Expired</h2>
                    <p class="text-gray-500 text-xs font-bold mt-2 tracking-[0.2em] uppercase">Daftar produk yang sudah kedaluwarsa dan dihapus dari persediaan aktif</p>
                    
                    <div class="flex justify-center gap-4 mt-6">

                        <a href="{{ route('monitoring.expired-history.export.pdf') }}" class="flex items-center gap-2 px-6 py-3 bg-[#E02424] hover:bg-[#B91C1C] text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg transform hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            PDF
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6] border-b-2 border-[#d4af37]/20">
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">ID</th>
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">BATCH</th>
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">PRODUK</th>
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">RASA</th>
                                <th class="px-6 py-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KATEGORI</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STOK SAAT INI</th>
                                <th class="px-6 py-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KERUGIAN PRODUK EXPIRED</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">KEMAS</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">EXP</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">STATUS</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">SISA</th>
                                <th class="px-6 py-6 text-center text-[13px] font-black text-black uppercase tracking-wider">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse($expiredHistories as $index => $history)
                                <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                    <td class="px-6 py-5 text-center border-r border-[#d4af37]/10">
                                        <div class="w-10 h-10 rounded-full bg-[#7a0e14] flex items-center justify-center text-white font-bold text-xs shadow-md mx-auto border-2 border-[#d4af37]/30">
                                            {{ $loop->iteration }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-800 font-bold border-r border-[#d4af37]/10 uppercase tracking-tight">{{ $history->no_batch }}</td>
                                    <td class="px-6 py-5 border-r border-[#d4af37]/10">
                                        <span class="text-base font-bold text-gray-900 group-hover:text-[#7a0e14] transition-colors line-clamp-1 uppercase tracking-tight">{{ $history->nama_produk }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-base text-gray-600 border-r border-[#d4af37]/10 italic font-medium">{{ $history->rasa_produk ?? '-' }}</td>
                                    <td class="px-6 py-5 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 truncate max-w-[150px] uppercase tracking-tighter">{{ $history->kategori }}</td>
                                    <td class="px-6 py-5 text-base text-red-600 font-black border-r border-[#d4af37]/10 text-center bg-red-50/50">{{ number_format($history->jumlah) }}</td>
                                    <td class="px-6 py-5 text-base text-[#7a0e14] font-black border-r border-[#d4af37]/10 text-right font-mono tracking-tighter">Rp {{ number_format($history->jumlah * ($history->hpp ?? 0), 0, ',', '.') }}</td>
                                    <td class="px-6 py-5 text-sm text-gray-700 border-r border-[#d4af37]/10 text-center font-bold">{{ $history->tgl_masuk ? $history->tgl_masuk->format('d/m/y') : '-' }}</td>
                                    <td class="px-6 py-5 text-sm text-red-600 border-r border-[#d4af37]/10 text-center font-black">{{ $history->tgl_expired ? $history->tgl_expired->format('d/m/y') : '-' }}</td>
                                    <td class="px-6 py-5 text-center border-r border-[#d4af37]/10">
                                        <div class="px-3 py-1 rounded-full bg-red-600 text-white text-[10px] font-black uppercase shadow-sm">Expired</div>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-red-600 font-black text-center border-r border-[#d4af37]/10">
                                        {{ $history->sisa_hari }} Hr
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-2">
@if(!$history->is_journaled)
                                                <form action="{{ route('monitoring.add-to-journal', $history->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-lg text-[10px] font-black uppercase shadow-lg hover:from-blue-700 hover:to-blue-900 transition-all flex items-center gap-1 group whitespace-nowrap" title="Masukkan ke Jurnal">
                                                        <svg class="w-3 h-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                        Jurnal
                                                    </button>
                                                </form>
@else
                                                <div class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg text-[10px] font-black uppercase flex items-center gap-1 cursor-not-allowed" title="Sudah di Jurnal">
                                                    <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    Sudah
                                                </div>
@endif

                                            <form action="{{ route('monitoring.expired-history.destroy', $history->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-xl text-[#ff6b6b] hover:bg-[#ff6b6b] hover:text-white transition-all transform hover:scale-110" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-gray-400 italic font-medium">Belum ada riwayat produk expired yang dihapus.</p>
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
