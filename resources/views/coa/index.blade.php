<x-app-layout>
    <div class="p-4 md:p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Main Card with Glassmorphism -->
            <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-white/50 overflow-hidden p-6 md:p-10 relative">
                
                <!-- Top Actions: Tambah COA & Search -->
                <div class="flex flex-col lg:flex-row justify-between items-center mb-6 gap-6">
                    <!-- Left: Tambah COA & Import Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('coa.create') }}" 
                           class="group relative inline-flex items-center justify-center px-8 py-3 font-bold text-white transition-all duration-300 bg-gradient-to-b from-[#4a779d] to-[#2d4e6d] rounded-xl shadow-[0_4px_0_0_#1a2f42] hover:shadow-[0_2px_0_0_#1a2f42] hover:translate-y-[2px] active:shadow-none active:translate-y-[4px]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah COA
                        </a>
                        
                        <!-- Import Excel REMOVED as requested --> 
                    </div>

                    <!-- Right: Search Form -->
                    <form action="{{ route('coa.index') }}" method="GET" class="flex items-center gap-3 w-full lg:w-auto">
                        <div class="relative flex-1 lg:w-80">
                            <input type="text" name="nama_akun_search" value="{{ $namaAkunSearch ?? '' }}" placeholder="Cari nama akun..." 
                                   class="w-full px-6 py-3 bg-[#fdfdfd] border-2 border-[#e5e5e5] rounded-xl shadow-inner focus:outline-none focus:border-[#d4af37] transition-all placeholder:text-gray-400">
                        </div>
                        <button type="submit" 
                                class="px-8 py-3 font-bold text-white bg-gradient-to-b from-[#b89553] to-[#8d6e35] rounded-xl shadow-[0_4px_0_0_#5a4622] hover:shadow-[0_2px_0_0_#5a4622] hover:translate-y-[2px] active:shadow-none active:translate-y-[4px] transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Subtle Divider -->
                <div class="border-b-2 border-[#d4af37]/20 mb-8"></div>

                <!-- Table Container -->
                <div class="overflow-y-auto max-h-[60vh] rounded-2xl border-2 border-[#d4af37]/30 shadow-lg">
                    <table class="w-full border-collapse">
                        <thead class="sticky top-0 z-10 shadow-sm">
                            <tr class="bg-gradient-to-b from-[#f2d9a6] to-[#e6c17a] border-b-2 border-[#d4af37]">
                                <th class="px-6 py-4 text-left font-black text-black uppercase tracking-wider w-20">NO</th>
                                <th class="px-6 py-4 text-left font-black text-black uppercase tracking-wider">KODE AKUN</th>

                                <th class="px-6 py-4 text-left font-black text-black uppercase tracking-wider">NAMA AKUN</th>
</tr>
                        </thead>
                        <tbody class="divide-y divide-[#d4af37]/20">
                            @forelse ($coa as $index => $coaItem)
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-[#fff9eb]' }} hover:bg-[#fff0d1] transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $coaItem->kode_akun }}</td>
                                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ $coaItem->nama_akun }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic bg-white">Data COA tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
