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

            <!-- Global Error Display -->
            @if ($errors->any())
                <div class="bg-red-500/20 backdrop-blur-md border border-red-500/50 text-red-100 font-bold px-6 py-4 rounded-2xl shadow-lg mb-8">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Glassmorphism Card for Form -->
            <div class="bg-white/95 backdrop-blur-md border-2 border-[#d4af37]/60 rounded-[40px] shadow-[0_30px_80px_rgba(0,0,0,0.4)] p-10">
                <div class="mb-10 text-center border-b border-[#d4af37]/20 pb-8">
                    <h2 class="text-3xl font-serif font-black text-[#7a0e14] drop-shadow-sm uppercase tracking-widest">Tambah Entry Produk Keluar</h2>
                    <p class="text-gray-500 text-xs font-bold mt-2 tracking-[0.2em] uppercase">Pencatatan penjualan / barang keluar</p>
                </div>

                <form method="POST" action="{{ route('produk-keluar.store') }}" class="space-y-8">
                    @csrf

                    <!-- Hidden fields -->
                    <input type="hidden" name="id_transaksi" value="TXN-{{ time() }}">
                    <input type="hidden" name="nama_produk" id="nama_produk_hidden" value="">
                    <input type="hidden" name="inventory_id" id="inventory_id_hidden" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- KATEGORI PRODUK -->
                        <div id="col_kategori" class="md:col-span-1">
                            <label for="kategori_filter" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">KATEGORI PRODUK</label>
                            <div class="relative group">
                                <select id="kategori_filter" class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->nama_kategori }}">{{ $cat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#d4af37]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- PILIH RASA (Conditional) -->
                        <div id="col_rasa" class="md:col-span-1">
                            <label for="rasa_filter" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PILIH RASA</label>
                            <div class="relative group">
                                <select id="rasa_filter" class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none">
                                    <option value="">Semua Rasa</option>
                                    @foreach($flavors as $flavor)
                                        <option value="{{ $flavor->nama_rasa }}">{{ $flavor->nama_rasa }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#d4af37]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- PILIH NO BATCH -->
                        <div id="col_produk" class="md:col-span-2">
                            <label for="kode_produk" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PILIH NO BATCH <span class="text-red-500 font-black">*</span></label>
                            <div class="relative group">
                                <select id="kode_produk" name="kode_produk" class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none" required>
                                    <option value="" data-kategori="" data-rasa="">Pilih Kategori dan Rasa Terlebih Dahulu</option>
                                    @foreach($inventoriesForSelect as $inventory)
                                        <option value="{{ $inventory->kode_produk }}" 
                                                data-id="{{ $inventory->id }}" 
                                                data-nama="{{ $inventory->nama_produk }}" 
                                                data-jumlah="{{ $inventory->jumlah }}" 
                                                data-harga="{{ $inventory->selling_price }}"
                                                data-cost="{{ $inventory->harga }}"
                                                data-hpp="{{ $inventory->hpp_master }}"
                                                data-kategori="{{ $inventory->kategori }}"
                                                data-rasa="{{ $inventory->rasa_produk }}"
                                                data-batch="{{ $inventory->no_batch }}"
                                                data-exp="{{ $inventory->tgl_expired ? \Carbon\Carbon::parse($inventory->tgl_expired)->format('d/m/Y') : 'No Exp' }}"
                                                class="hidden_option" style="display: none;">
                                            Exp: {{ $inventory->tgl_expired ? \Carbon\Carbon::parse($inventory->tgl_expired)->format('d/m/Y') : 'No Exp' }} | No Batch: {{ $inventory->no_batch ?? '-' }} | Tersedia: {{ $inventory->jumlah }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#d4af37]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('kode_produk')" class="mt-2" />
                        </div>
                    </div>





                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- TANGGAL -->
                        <div>
                            <label for="tanggal" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">TANGGAL KELUAR <span class="text-red-500 font-black">*</span></label>
                            <input id="tanggal" type="date" name="tanggal" value="{{ date('Y-m-d') }}" 
                                   class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" required />
                        </div>

                        <!-- HARGA SATUAN (VISIBLE) -->
                        <div>
                            <label for="harga_display" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">HARGA SATUAN</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="harga_display" type="text" readonly
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black shadow-inner" 
                                       placeholder="0" />
                                <input id="harga" type="hidden" name="harga" />
                            </div>
                        </div>

                        <!-- Spacer Row 1 -->
                        <div class="hidden md:block"></div>



                        <!-- HP Produksi -->
                        <div>
                            <label for="harga_pokok_per_pack_display" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">HP Produksi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="harga_pokok_per_pack_display" type="text"
                                       class="w-full bg-white border-2 border-[#d4af37]/30 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" 
                                       placeholder="0" />
                                <input id="harga_pokok_per_pack" type="hidden" name="harga_pokok_per_pack" value="0" />
                            </div>
                        </div>

                        <!-- JUMLAH PACK KELUAR -->
                        <div>
                            <label for="jumlah_keluar" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">JUMLAH PACK KELUAR</label>
                            <input id="jumlah_keluar" type="number" name="jumlah_keluar" value="{{ old('jumlah_keluar') }}" 
                                   class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-400" 
                                   min="0" placeholder="0" />
                            <input type="hidden" name="jumlah_pack_keluar" id="jumlah_pack_keluar_hidden">
                        </div>

                        <!-- PERSEDIAAN PRODUK KELUAR -->
                        <div>
                            <label for="persediaan_produk_keluar_display" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PERSEDIAAN PRODUK KELUAR</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="persediaan_produk_keluar_display" type="text" placeholder="0" readonly
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black shadow-inner" />
                            </div>
                        </div>
                    </div>



                    <!-- TOTAL HARGA (Hidden) -->
                    <input id="total_harga" type="hidden" name="total_harga" value="0" />

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#d4af37] shadow-xl hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[80px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Transaksi Keluar</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Glassmorphism Card for History Table -->
            <div class="bg-white/95 backdrop-blur-md border-2 border-[#d4af37]/60 rounded-[40px] shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden mt-8 p-10">
                <div class="mb-10 text-center border-b border-[#d4af37]/20 pb-6">
                    <h2 class="text-3xl font-serif font-black text-[#7a0e14] drop-shadow-sm uppercase tracking-widest">Riwayat Produk Keluar</h2>
                    <p class="text-gray-500 text-xs font-bold mt-2 tracking-[0.2em] uppercase">Daftar pencatatan stok keluar terperinci</p>
                </div>

                <form method="GET" action="{{ route('produk-keluar.index') }}" class="mb-8 flex flex-col md:flex-row gap-4">
                    <input id="search" type="text" name="search" value="{{ old('search', $search) }}" 
                           class="flex-1 bg-[#fdf9eb] border-2 border-[#d4af37]/30 text-gray-700 placeholder-gray-400 rounded-2xl px-6 py-4 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" 
                           placeholder="Cari berdasarkan ID Produk atau Nama Produk" />
                    <button type="submit" class="bg-[#d4af37] hover:bg-[#b5952f] text-[#5a0f12] font-black px-10 py-4 rounded-2xl shadow-lg uppercase tracking-wider transition-all border-2 border-[#d4af37]">
                        Cari
                    </button>
                </form>

                <div class="overflow-hidden rounded-3xl border-2 border-[#d4af37]/20 shadow-inner">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full w-full whitespace-nowrap">
                            <thead>
                                <tr class="bg-gradient-to-r from-[#f8f4e6] via-[#ffffff] to-[#f8f4e6] border-b-2 border-[#d4af37]/20">
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">ID Transaksi</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Tanggal</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Produk</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Kategori</th>
                                    <th class="py-6 px-6 text-left text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Tgl Expired & No Batch</th>
                                    <th class="py-6 px-6 text-center text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Jumlah Pack Keluar</th>
                                    <th class="py-6 px-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">HP Produksi</th>
                                    <th class="py-6 px-6 text-right text-[13px] font-black text-black uppercase tracking-wider border-r border-[#d4af37]/10 whitespace-nowrap">Persediaan Produk Keluar</th>
                                    <th class="py-6 px-6 text-center text-[13px] font-black text-black uppercase tracking-wider whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#d4af37]/10">
                                @forelse($entries as $index => $entry)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-[#fdf9eb]' : 'bg-[#f4ebd0]' }} hover:bg-[#ffeec2] transition-colors group">
                                        <td class="py-5 px-6 text-sm font-bold text-gray-700 border-r border-[#d4af37]/10">{{ $entry->id_transaksi }}</td>
                                        <td class="py-5 px-6 text-sm font-medium text-gray-600 border-r border-[#d4af37]/10">{{ \Carbon\Carbon::parse($entry->tanggal)->format('d/m/Y') }}</td>
                                        <td class="py-5 px-6 border-r border-[#d4af37]/10">
                                            <div class="text-sm font-bold text-gray-900 uppercase">{{ $entry->nama_produk }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $entry->kode_produk }}</div>
                                        </td>
                                        <td class="py-5 px-6 text-sm text-[#7a0e14] font-black border-r border-[#d4af37]/10 uppercase tracking-tighter">{{ $entry->kategori ?? '-' }}</td>
                                        <td class="py-5 px-6 border-r border-[#d4af37]/10">
                                            @if($entry->inventory)
                                                <div class="text-sm font-bold text-[#7a0e14] uppercase">Exp: {{ $entry->inventory->tgl_expired ? \Carbon\Carbon::parse($entry->inventory->tgl_expired)->format('d/m/Y') : '-' }}</div>
                                                <div class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Batch: {{ $entry->inventory->no_batch ?? '-' }}</div>
                                            @else
                                                <div class="text-sm text-gray-400 font-bold">-</div>
                                            @endif
                                        </td>
                                        <td class="py-5 px-6 text-base font-black text-blue-600 border-r border-[#d4af37]/10 text-center">{{ $entry->jumlah_pack_keluar ?? $entry->jumlah_keluar }}</td>
                                        <td class="py-5 px-6 text-base font-black text-[#7a0e14] border-r border-[#d4af37]/10 text-right font-mono">Rp {{ number_format($entry->harga_pokok_per_pack, 2, ',', '.') }}</td>
                                        <td class="py-5 px-6 text-base font-black text-[#7a0e14] border-r border-[#d4af37]/10 text-right font-mono text-orange-600">Rp {{ number_format(($entry->jumlah_pack_keluar ?? $entry->jumlah_keluar) * $entry->harga_pokok_per_pack, 2, ',', '.') }}</td>
                                        <td class="py-5 px-6 text-sm">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('produk-keluar.show', $entry->id) }}" class="p-2.5 rounded-xl text-blue-600 hover:bg-blue-100 transition-all transform hover:scale-110" title="Lihat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                </a>
                                                <a href="{{ route('produk-keluar.edit', $entry->id) }}" class="p-2.5 rounded-xl text-[#b89553] hover:bg-[#d4af37] hover:text-white transition-all transform hover:scale-110" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </a>
                                                <form method="POST" action="{{ route('produk-keluar.destroy', $entry->id) }}" class="inline" onsubmit="return confirm('Hapus transaksi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2.5 rounded-xl text-red-600 hover:bg-red-100 transition-all transform hover:scale-110" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-20 px-6 text-center">
                                            <div class="flex flex-col items-center gap-4">
                                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="text-gray-400 italic font-medium">Tidak ada data transaksi keluar.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8">
                    {{ $entries->links() }}
                </div>
            </div>
        </div>

        <script>
            const allProductsData = {!! isset($allProductsData) ? $allProductsData->toJson() : '[]' !!};
            
            const flavorStockData = {!! $inventoriesForSelect->mapWithKeys(function($item) {
                return [$item->kategori . '|' . $item->rasa_produk => $item->total_jumlah];
            })->toJson() !!};

            const masterCategoryPrices = {!! $categoryPrices->toJson() !!};

            document.addEventListener('DOMContentLoaded', function() {
                const kategoriFilter = document.getElementById('kategori_filter');
                const idProdukSelect = document.getElementById('kode_produk');
                const productOptions = Array.from(idProdukSelect.options);
                const namaProdukHidden = document.getElementById('nama_produk_hidden');
                const inventoryIdHidden = document.getElementById('inventory_id_hidden');

                const jumlahKeluarInput = document.getElementById('jumlah_keluar');
                const jumlahPackKeluarHidden = document.getElementById('jumlah_pack_keluar_hidden');
                const hargaInput = document.getElementById('harga');
                const hargaDisplay = document.getElementById('harga_display');
                const hargaPokokInput = document.getElementById('harga_pokok_per_pack');
                const hargaPokokDisplay = document.getElementById('harga_pokok_per_pack_display');
                const totalHargaInput = document.getElementById('total_harga');



                function calculateTotal() {
                    let totalQty = parseInt(jumlahKeluarInput.value) || 0;
                    const hSatuan = parseFloat(hargaInput.value) || 0;
                    let grandTotal = totalQty * hSatuan;

                    jumlahKeluarInput.readOnly = false;
                    jumlahKeluarInput.classList.remove('bg-[#f4ebd0]');
                    jumlahKeluarInput.classList.add('bg-[#fdf9eb]');
                    
                    jumlahPackKeluarHidden.value = totalQty;
                    totalHargaInput.value = Math.round(grandTotal);
                    
                    updatePersediaanKeluar();
                }





                kategoriFilter.addEventListener('change', function() {
                    const selectedKategori = this.value;
                    const selectedRasa = document.getElementById('rasa_filter').value;
                    
                    idProdukSelect.value = "";
                    let hasOptions = false;
                    
                    productOptions.forEach(option => {
                        if (option.value === "") return;
                        
                        const optionKategori = option.getAttribute('data-kategori');
                        const optionRasa = option.getAttribute('data-rasa');
                        
                        const matchKategori = !selectedKategori || optionKategori === selectedKategori;
                        const matchRasa = !selectedRasa || optionRasa === selectedRasa;
                        
                        if (matchKategori && matchRasa) {
                            option.style.display = "";
                            hasOptions = true;
                        } else {
                            option.style.display = "none";
                        }
                    });

                    if (!selectedKategori && !selectedRasa) {
                        idProdukSelect.options[0].text = "Pilih Kategori dan Rasa Terlebih Dahulu";
                    } else if (hasOptions) {
                        idProdukSelect.options[0].text = "Silakan Pilih No Batch";
                    } else {
                        idProdukSelect.options[0].text = "Tidak Ada Batch Tersedia";
                    }

                    // Update price automatically if category selected
                    if (selectedKategori) {
                        const masterPrice = masterCategoryPrices[selectedKategori] || 0;
                        hargaInput.value = masterPrice;
                        hargaDisplay.value = parseFloat(masterPrice).toLocaleString('id-ID');
                    } else if (!selectedKategori) {
                        hargaInput.value = '';
                        hargaDisplay.value = '';
                    }

                    calculateTotal();
                    idProdukSelect.dispatchEvent(new Event('change'));
                });

                document.getElementById('rasa_filter').addEventListener('change', function() {
                    kategoriFilter.dispatchEvent(new Event('change'));
                });

                idProdukSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const id = selectedOption.getAttribute('data-id');
                    const nama = selectedOption.getAttribute('data-nama');
                    const jumlah = selectedOption.getAttribute('data-jumlah');
                    const productHarga = selectedOption.getAttribute('data-harga');
                    const productCost = selectedOption.getAttribute('data-cost');
                    const hpp = selectedOption.getAttribute('data-hpp');
                    const kategori = selectedOption.getAttribute('data-kategori');
                    
                    if (id) {
                        inventoryIdHidden.value = id;
                        namaProdukHidden.value = nama;
                        hargaInput.value = productHarga;
                        hargaDisplay.value = parseFloat(productHarga).toLocaleString('id-ID');
                        
                        if (hpp) {
                            hargaPokokInput.value = hpp;
                            hargaPokokDisplay.value = parseFloat(hpp).toLocaleString('id-ID');
                        } else {
                            hargaPokokInput.value = '0';
                            hargaPokokDisplay.value = '';
                        }
                        
                        // Remove max limit to allow FEFO spillover
                        jumlahKeluarInput.removeAttribute('max');
                        jumlahKeluarInput.placeholder = 'Ketik Jumlah Keluar...';
                        
                        // Add hint about FEFO
                        let fefoHint = document.getElementById('fefo-hint');
                        if (!fefoHint) {
                            fefoHint = document.createElement('div');
                            fefoHint.id = 'fefo-hint';
                            fefoHint.className = 'text-xs text-orange-600 font-bold mt-1';
                            jumlahKeluarInput.parentNode.appendChild(fefoHint);
                        }
                        fefoHint.innerHTML = `Stok batch ini: ${jumlah}. Jika melebihi, sisa akan otomatis memotong batch berikutnya (FEFO).`;

                        calculateTotal();
                        updatePersediaanKeluar();
                    } else {
                        inventoryIdHidden.value = '';
                        namaProdukHidden.value = '';
                        
                        // Keep category price if category is selected, even if product is not
                        const selectedKategori = kategoriFilter.value;
                        const selectedRasa = document.getElementById('rasa_filter').value;
                        
                        if (selectedKategori) {
                            const masterPrice = masterCategoryPrices[selectedKategori] || 0;
                            hargaInput.value = masterPrice;
                            hargaDisplay.value = parseFloat(masterPrice).toLocaleString('id-ID');
                        } else {
                            hargaInput.value = '';
                            hargaDisplay.value = '';
                        }
                        
                        if (selectedKategori && selectedRasa) {
                            const masterProd = allProductsData.find(p => p.kategori === selectedKategori && p.rasa_produk === selectedRasa);
                            if (masterProd && masterProd.hpp) {
                                hargaPokokInput.value = masterProd.hpp;
                                hargaPokokDisplay.value = parseFloat(masterProd.hpp).toLocaleString('id-ID');
                            } else {
                                hargaPokokInput.value = '0';
                                hargaPokokDisplay.value = '';
                            }
                        } else {
                            hargaPokokInput.value = '0';
                            hargaPokokDisplay.value = '';
                        }
                        
                        jumlahKeluarInput.max = 0;
                        jumlahKeluarInput.placeholder = '0';
                        totalHargaInput.value = '0';
                        updatePersediaanKeluar();
                    }
                });



                jumlahKeluarInput.addEventListener('input', calculateTotal);

                function updatePersediaanKeluar() {
                    const qtyKeluar = document.getElementById('jumlah_keluar');
                    const qtyValue = parseInt(qtyKeluar.value) || 0;
                    const costPerPack = parseFloat(hargaPokokInput.value) || 0;
                    const totalPersediaanKeluar = qtyValue * costPerPack;
                    
                    const persediaanKeluarDisplay = document.getElementById('persediaan_produk_keluar_display');
                    persediaanKeluarDisplay.value = totalPersediaanKeluar.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                hargaPokokDisplay.addEventListener('input', function() {
                    const value = this.value.replace(/[^0-9]/g, '');
                    hargaPokokInput.value = value;
                    if (value) {
                        this.value = parseInt(value).toLocaleString('id-ID');
                    }
                    updatePersediaanKeluar();
                });
            });
        </script>
    </div>
</x-app-layout>
