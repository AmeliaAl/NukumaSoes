<x-app-layout>
    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="w-full max-w-7xl mx-auto px-6 relative z-10 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-500/20 backdrop-blur-md border border-green-500/50 text-green-100 font-bold px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Glassmorphism Card for Form -->
            <div class="bg-white/95 backdrop-blur-md border-2 border-[#d4af37]/60 rounded-[40px] shadow-[0_30px_80px_rgba(0,0,0,0.4)] p-10">
                <div class="mb-10 text-center border-b border-[#d4af37]/20 pb-8">
                    <h2 class="text-3xl font-serif font-black text-[#7a0e14] drop-shadow-sm uppercase tracking-widest">Tambah Entry Produk Masuk</h2>
                    <p class="text-gray-500 text-xs font-bold mt-2 tracking-[0.2em] uppercase">Pencatatan stok masuk ke toko</p>
                </div>

                <form method="POST" action="{{ route('persediaan.store') }}" class="space-y-8">
                    @csrf
                    
                    <input type="hidden" name="id_transaksi" value="TXN-{{ time() }}">
                    <input type="hidden" name="inventory_id" id="inventory_id_hidden" value="">
                    <input type="hidden" name="kode_produk" id="kode_produk_hidden" value="">
                    <input type="hidden" name="nama_produk" id="nama_produk_hidden" value="">
                    <input type="hidden" name="harga" id="harga_hidden" value="">
                    <input type="hidden" name="total_harga" id="total_harga_hidden" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- PRODUK ID -->
                        <div>
                            <label for="kode_produk" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PRODUK ID <span class="text-red-500 font-black">*</span></label>
                            <div class="relative group">
                                <select id="kode_produk" 
                                        class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('kode_produk') border-red-400 @enderror" 
                                        required autofocus>
                                    <option value="">Pilih Produk ID</option>
                                    @foreach($inventories as $inv)
                                        <option value="{{ $inv->id }}" 
                                                data-nama="{{ $inv->nama_produk }}" 
                                                data-harga="{{ $inv->harga ?? 0 }}" 
                                                data-stok="{{ $inv->jumlah }}"
                                                data-batch="{{ $inv->no_batch ?? '' }}"
                                                data-kode="{{ $inv->kode_produk }}"
                                                data-bbb="{{ $inv->product->bbb ?? 0 }}"
                                                data-btkl="{{ $inv->product->btkl ?? 0 }}"
                                                data-bop="{{ $inv->product->bop ?? 0 }}"
                                                data-hpp="{{ $inv->product->hpp ?? 0 }}">
                                            {{ $inv->kode_produk }} - {{ $inv->nama_produk }} - {{ $inv->no_batch ?? '-' }} ({{ $inv->kategori ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#d4af37]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('kode_produk')" class="mt-2" />
                        </div>

                        <!-- STOK SAAT INI -->
                        <div>
                            <label class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">STOK SAAT INI</label>
                            <div class="relative">
                                <input id="stok_saat_ini" type="text" readonly 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl px-5 py-4 text-[#7a0e14] font-black text-xl shadow-inner" 
                                       placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- NO BATCH -->
                        <div>
                            <label for="no_batch" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">NO BATCH</label>
                            <input id="no_batch" type="text" name="no_batch" value="{{ old('no_batch') }}" 
                                   class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-400" 
                                   placeholder="Input Manual No Batch" required autocomplete="no_batch" />
                            <x-input-error :messages="$errors->get('no_batch')" class="mt-2" />
                        </div>

                        <!-- TANGGAL -->
                        <div>
                            <label for="tanggal" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">TANGGAL MASUK <span class="text-red-500 font-black">*</span></label>
                            <input id="tanggal" type="date" name="tanggal" value="{{ date('Y-m-d') }}" 
                                   class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" 
                                   required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- JUMLAH PACK MASUK -->
                        <div>
                            <label for="jumlah_masuk" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">JUMLAH PACK MASUK <span class="text-red-500 font-black">*</span></label>
                            <input id="jumlah_masuk" type="number" name="jumlah_masuk" value="{{ old('jumlah_masuk') }}" 
                                   class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-400" 
                                   min="1" required autocomplete="jumlah_masuk" />
                            <x-input-error :messages="$errors->get('jumlah_masuk')" class="mt-2" />
                        </div>

                        <!-- HARGA POKOK PRODUKSI -->
                        <div>
                            <label for="harga_pokok_produksi" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">HARGA POKOK PRODUKSI</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="harga_pokok_produksi" type="text" name="harga_pokok_produksi" value="{{ old('harga_pokok_produksi') }}" 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black text-xl shadow-inner" 
                                       readonly />
                            </div>
                            <x-input-error :messages="$errors->get('harga_pokok_produksi')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- BIAYA BAHAN BAKU (BBB) -->
                        <div>
                            <label for="bbb" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">BIAYA BAHAN BAKU (BBB)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="bbb" type="text" name="bbb" value="{{ old('bbb') }}" 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black shadow-inner" 
                                       readonly />
                            </div>
                            <x-input-error :messages="$errors->get('bbb')" class="mt-2" />
                        </div>

                        <!-- BIAYA TENAGA KERJA LANGSUNG (BTKL) -->
                        <div>
                            <label for="btkl" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">BIAYA TENAGA KERJA LANGSUNG (BTKL)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="btkl" type="text" name="btkl" value="{{ old('btkl') }}" 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black shadow-inner" 
                                       readonly />
                            </div>
                            <x-input-error :messages="$errors->get('btkl')" class="mt-2" />
                        </div>

                        <!-- BIAYA OVERHEAD PABRIK (BOP) -->
                        <div>
                            <label for="bop" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">BIAYA OVERHEAD PABRIK (BOP)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="bop" type="text" name="bop" value="{{ old('bop') }}" 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black shadow-inner" 
                                       readonly />
                            </div>
                            <x-input-error :messages="$errors->get('bop')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- HARGA PER PCS -->
                        <div>
                            <label for="harga" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">HARGA PER PCS <span class="text-red-500 font-black">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="harga_display" type="text" readonly 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black text-xl shadow-inner" />
                            </div>
                        </div>

                        <!-- PERSEDIAAN PRODUK JADI -->
                        <div>
                            <label for="total_harga" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PERSEDIAAN PRODUK JADI <span class="text-red-500 font-black">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="total_harga_display" type="text" readonly 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black text-xl shadow-inner" />
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#d4af37] shadow-xl hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[80px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Entry Masuk</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Glassmorphism Card for History Table -->
            <div class="bg-white/95 backdrop-blur-md border-2 border-[#d4af37]/60 rounded-[40px] shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden mt-8 p-10">
                <div class="mb-10 text-center border-b border-[#d4af37]/20 pb-6">
                    <h2 class="text-3xl font-serif font-black text-[#7a0e14] drop-shadow-sm uppercase tracking-widest">Riwayat Produk Masuk</h2>
                    <p class="text-gray-500 text-xs font-bold mt-2 tracking-[0.2em] uppercase">Daftar pencatatan stok masuk terperinci</p>
                </div>

                <form method="GET" action="{{ route('persediaan.index') }}" class="mb-8 flex flex-col md:flex-row gap-4">
                    <input id="search" type="text" name="search" value="{{ old('search', $search) }}" 
                           class="flex-1 bg-[#fdf9eb] border-2 border-[#d4af37]/30 text-gray-700 placeholder-gray-400 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" 
                           placeholder="Cari berdasarkan No Batch atau Nama Produk" />
                    <button type="submit" class="bg-[#d4af37] hover:bg-[#b5952f] text-[#5a0f12] font-black px-10 py-4 rounded-2xl shadow-lg uppercase tracking-wider transition-all border-2 border-[#d4af37]">
                        Cari
                    </button>
                </form>

                <div class="overflow-hidden rounded-3xl border-2 border-[#d4af37]/20 shadow-inner">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full w-full whitespace-nowrap">
                            <thead>
                                <tr class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6] border-b-2 border-[#d4af37]/20">
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Transaksi</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Tanggal</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">No Batch</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Nama Produk</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Kategori</th>
                                    <th class="py-6 px-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Jumlah Pack Masuk</th>
                                    <th class="py-6 px-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Harga Per Pcs</th>
                                    <th class="py-6 px-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Persediaan Produk Jadi</th>
                                    <th class="py-6 px-6 text-center text-[13px] font-black text-black uppercase tracking-wider whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#d4af37]/10">
                                @forelse($entries as $index => $entry)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                        <td class="py-5 px-6 text-sm font-bold text-gray-700 border-r border-[#d4af37]/10">{{ $entry->id_transaksi }}</td>
                                        <td class="py-5 px-6 text-sm font-medium text-gray-600 border-r border-[#d4af37]/10">{{ \Carbon\Carbon::parse($entry->tanggal)->format('d M Y') }}</td>
                                        <td class="py-5 px-6 text-sm font-bold text-[#7a0e14] border-r border-[#d4af37]/10">{{ $entry->no_batch ?? $entry->kode_produk }}</td>
                                        <td class="py-5 px-6 text-sm font-bold text-gray-900 border-r border-[#d4af37]/10 uppercase">{{ $entry->nama_produk }}</td>
                                        <td class="py-5 px-6 text-sm font-bold text-[#7a0e14] border-r border-[#d4af37]/10 uppercase">{{ $entry->inventory->kategori ?? '-' }}</td>
                                        <td class="py-5 px-6 text-base font-black text-green-600 border-r border-[#d4af37]/10 text-center">{{ number_format($entry->jumlah_masuk) }}</td>
                                        <td class="py-5 px-6 text-sm font-bold text-gray-800 border-r border-[#d4af37]/10 text-right font-mono">Rp {{ number_format($entry->category_price, 2, ',', '.') }}</td>
                                        <td class="py-5 px-6 text-base font-black text-[#7a0e14] border-r border-[#d4af37]/10 text-right font-mono">Rp {{ number_format($entry->total_harga, 2, ',', '.') }}</td>
                                        <td class="py-5 px-6 text-sm">
                                            <div class="flex justify-center items-center space-x-3">
                                                <a href="{{ route('persediaan.show', $entry->id) }}" class="p-2.5 rounded-xl text-blue-600 hover:bg-blue-100 transition-all transform hover:scale-110" title="Lihat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                </a>
                                                <a href="{{ route('persediaan.edit', $entry->id) }}" class="p-2.5 rounded-xl text-[#b89553] hover:bg-[#d4af37] hover:text-white transition-all transform hover:scale-110" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </a>
                                                <div x-data="{ showDeleteModal: false }" class="inline">
                                                    <button @click="showDeleteModal = true" type="button" class="p-2.5 rounded-xl text-red-600 hover:bg-red-100 transition-all transform hover:scale-110" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                    
                                                    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                                                         x-transition:enter="transition ease-out duration-300"
                                                         x-transition:enter-start="opacity-0 scale-95"
                                                         x-transition:enter-end="opacity-100 scale-100"
                                                         x-transition:leave="transition ease-in duration-200"
                                                         x-transition:leave-start="opacity-100 scale-100"
                                                         x-transition:leave-end="opacity-0 scale-95">
                                                        <div @click.away="showDeleteModal = false" class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-auto border-2 border-red-100">
                                                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                            </div>
                                                            <h3 class="text-xl font-black text-center text-gray-900 mb-2 uppercase tracking-wide">Konfirmasi Hapus</h3>
                                                            <p class="text-center text-gray-500 mb-6 font-medium text-sm">Apakah Anda yakin ingin menghapus riwayat produk masuk ini? Data stok akan disesuaikan kembali.</p>
                                                            <div class="flex justify-center gap-3">
                                                                <button @click="showDeleteModal = false" type="button" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors uppercase tracking-wider">Batal</button>
                                                                <form method="POST" action="{{ route('persediaan.destroy', $entry->id) }}" class="inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-lg shadow-red-600/30 transition-all uppercase tracking-wider">Ya, Hapus</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-20 px-6 text-center">
                                            <div class="flex flex-col items-center gap-4">
                                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                                <p class="text-gray-400 italic font-medium">Tidak ada data produk masuk.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $entries->links() }}
                </div>
            </div>
        </div>

        <script>
            const productsData = {!! $products->map(function($p) {
                return [
                    'kode_produk' => $p->kode_produk,
                    'total_stok' => $p->jumlah,
                    'inventories' => $p->inventories->map(function($inv) {
                        return [
                            'no_batch' => $inv->no_batch,
                            'jumlah' => $inv->jumlah
                        ];
                    })
                ];
            })->toJson() !!};

            document.addEventListener('DOMContentLoaded', function() {
                const idProdukSelect = document.getElementById('kode_produk');
                const namaProdukHidden = document.getElementById('nama_produk_hidden');
                const kodeProdukHidden = document.getElementById('kode_produk_hidden');
                const hargaHidden = document.getElementById('harga_hidden');
                const totalHargaHidden = document.getElementById('total_harga_hidden');
                const hargaInput = document.getElementById('harga_display');
                const jumlahMasukInput = document.getElementById('jumlah_masuk');
                const totalHargaInput = document.getElementById('total_harga_display');
                const noBatchInput = document.getElementById('no_batch');
                const stokSaatIniInput = document.getElementById('stok_saat_ini');

                const bbbInput = document.getElementById('bbb');
                const btklInput = document.getElementById('btkl');
                const bopInput = document.getElementById('bop');
                const hargaPokokProduksiInput = document.getElementById('harga_pokok_produksi');

                function calculateTotalHarga() {
                    const bbb = parseFloat(bbbInput.dataset.raw) || 0;
                    const btkl = parseFloat(btklInput.dataset.raw) || 0;
                    const bop = parseFloat(bopInput.dataset.raw) || 0;
                    const jumlah = parseInt(jumlahMasukInput.value) || 0;
                    const total = (bbb + btkl + bop) * jumlah;
                    
                    totalHargaHidden.value = total;
                    if (total > 0) {
                        totalHargaInput.value = total.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    } else {
                        totalHargaInput.value = '';
                    }
                }

                function updateStokDisplay() {
                    const selectedOption = idProdukSelect.options[idProdukSelect.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        const currentStok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
                        const addedStok = parseInt(jumlahMasukInput.value) || 0;
                        stokSaatIniInput.value = (currentStok + addedStok).toLocaleString('id-ID');
                    } else {
                        stokSaatIniInput.value = '';
                    }
                }

                idProdukSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const nama = selectedOption.getAttribute('data-nama');
                    const harga = selectedOption.getAttribute('data-harga');
                    const batch = selectedOption.getAttribute('data-batch');
                    const kode = selectedOption.getAttribute('data-kode');
                    
                    const bbb = selectedOption.getAttribute('data-bbb');
                    const btkl = selectedOption.getAttribute('data-btkl');
                    const bop = selectedOption.getAttribute('data-bop');
                    const hpp = selectedOption.getAttribute('data-hpp');

                    if (nama) {
                        namaProdukHidden.value = nama;
                        kodeProdukHidden.value = kode || '';
                        hargaHidden.value = harga;
                        hargaInput.value = parseFloat(harga).toLocaleString('id-ID');
                        noBatchInput.value = batch || '';
                        
                        const bbbVal = bbb && parseFloat(bbb) > 0 ? parseFloat(bbb) : 0;
                        const btklVal = btkl && parseFloat(btkl) > 0 ? parseFloat(btkl) : 0;
                        const bopVal = bop && parseFloat(bop) > 0 ? parseFloat(bop) : 0;
                        const hppVal = hpp && parseFloat(hpp) > 0 ? parseFloat(hpp) : 0;

                        bbbInput.value = bbbVal > 0 ? bbbVal.toLocaleString('id-ID') : '';
                        btklInput.value = btklVal > 0 ? btklVal.toLocaleString('id-ID') : '';
                        bopInput.value = bopVal > 0 ? bopVal.toLocaleString('id-ID') : '';
                        hargaPokokProduksiInput.value = hppVal > 0 ? hppVal.toLocaleString('id-ID') : '';
                        
                        // Store raw values for calculation
                        bbbInput.dataset.raw = bbbVal;
                        btklInput.dataset.raw = btklVal;
                        bopInput.dataset.raw = bopVal;
                        
                        updateStokDisplay();
                        calculateTotalHarga();
                    } else {
                        namaProdukHidden.value = '';
                        kodeProdukHidden.value = '';
                        hargaHidden.value = '';
                        hargaInput.value = '';
                        stokSaatIniInput.value = '';
                        totalHargaHidden.value = '';
                        totalHargaInput.value = '';
                        noBatchInput.value = '';
                        
                        bbbInput.value = '';
                        btklInput.value = '';
                        bopInput.value = '';
                        hargaPokokProduksiInput.value = '';
                    }
                });

                // Trigger change on load if there's a selected value
                if (idProdukSelect.value) {
                    idProdukSelect.dispatchEvent(new Event('change'));
                }

                jumlahMasukInput.addEventListener('input', function() {
                    calculateTotalHarga();
                    updateStokDisplay();
                });
                bbbInput.addEventListener('input', calculateTotalHarga);
                btklInput.addEventListener('input', calculateTotalHarga);
                bopInput.addEventListener('input', calculateTotalHarga);

                hargaPokokProduksiInput.addEventListener('input', function() {
                    // readonly, no action needed
                });
            });
        </script>
    </div>
</x-app-layout>
