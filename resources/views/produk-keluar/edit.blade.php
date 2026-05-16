<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-[#f8f4e6] to-[#ffffff] py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-10">
                <a href="{{ route('produk-keluar.index') }}" class="group flex items-center text-[#7a0e14] font-black uppercase tracking-widest text-sm bg-white/80 backdrop-blur-md px-6 py-3 rounded-2xl border-2 border-[#d4af37]/30 shadow-sm hover:border-[#d4af37] transition-all">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali Ke Riwayat
                </a>
                <div class="text-right">
                    <h1 class="text-4xl font-serif font-black text-[#7a0e14] drop-shadow-sm uppercase tracking-tighter">Edit Transaksi</h1>
                    <p class="text-[#d4af37] font-bold text-xs uppercase tracking-[0.3em]">Update Detail Produk Keluar</p>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white/95 backdrop-blur-md border-2 border-[#d4af37]/60 rounded-[40px] shadow-[0_30px_80px_rgba(0,0,0,0.4)] overflow-hidden p-10">
                <form method="POST" action="{{ route('produk-keluar.update', $entry->id) }}" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- ID TRANSAKSI -->
                        <div>
                            <label for="id_transaksi" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">ID TRANSAKSI</label>
                            <input id="id_transaksi" type="text" name="id_transaksi" value="{{ old('id_transaksi', $entry->id_transaksi) }}" 
                                   class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl px-5 py-4 text-[#7a0e14] font-black shadow-inner opacity-70 cursor-not-allowed" readonly />
                        </div>

                        <!-- TANGGAL -->
                        <div>
                            <label for="tanggal" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">TANGGAL KELUAR</label>
                            <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', $entry->tanggal) }}" 
                                   class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- PRODUK -->
                        <div>
                            <label for="nama_produk" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PRODUK</label>
                            <input id="nama_produk" type="text" name="nama_produk" value="{{ old('nama_produk', $entry->nama_produk) }}" 
                                   class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl px-5 py-4 text-[#7a0e14] font-black shadow-inner opacity-70 cursor-not-allowed" readonly />
                            <input type="hidden" name="kode_produk" value="{{ $entry->kode_produk }}">
                        </div>

                        <!-- KATEGORI -->
                        <div>
                            <label class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">KATEGORI</label>
                            <input type="text" value="{{ $entry->kategori }}" class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl px-5 py-4 text-[#7a0e14] font-black shadow-inner opacity-70 cursor-not-allowed" readonly />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 bg-[#fdf9eb] p-8 rounded-[30px] border border-[#d4af37]/20">
                        <!-- JUMLAH PACK (MANUAL) -->
                        <div>
                            <label for="jumlah_pack" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">JUMLAH PACK (MANUAL)</label>
                            <input id="jumlah_pack" type="number" name="jumlah_pack" value="{{ old('jumlah_pack', $entry->jumlah_pack) }}" 
                                   class="w-full bg-white border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" />
                        </div>

                        <!-- PERSEDIAAN PRODUK JADI -->
                        <div>
                            <label for="harga_persediaan_produk_jadi_display" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PERSEDIAAN PRODUK JADI</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="harga_persediaan_produk_jadi_display" type="text" 
                                       value="{{ number_format($entry->harga_persediaan_produk_jadi, 0, ',', '.') }}"
                                       class="w-full bg-white border-2 border-[#d4af37]/30 rounded-2xl pl-12 pr-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" />
                                <input id="harga_persediaan_produk_jadi" type="hidden" name="harga_persediaan_produk_jadi" value="{{ $entry->harga_persediaan_produk_jadi }}" />
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="hidden md:block"></div>

                        <!-- Spacer -->
                        <div class="hidden md:block"></div>

                        <!-- HARGA POKOK PER PACK -->
                        <div>
                            <label class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">HARGA POKOK/PACK</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="harga_pokok_per_pack_display" type="text" readonly
                                       value="{{ number_format($entry->harga_pokok_per_pack, 2, ',', '.') }}"
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black shadow-inner" />
                                <input id="harga_pokok_per_pack" type="hidden" name="harga_pokok_per_pack" value="{{ $entry->harga_pokok_per_pack }}" />
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="hidden md:block"></div>

                        <!-- JUMLAH PACK KELUAR -->
                        <div>
                            <label for="jumlah_keluar" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">JUMLAH PACK KELUAR</label>
                            <input id="jumlah_keluar" type="number" name="jumlah_keluar" value="{{ old('jumlah_keluar', $entry->jumlah_keluar) }}" 
                                   class="w-full bg-white border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all" />
                        </div>

                        <!-- PERSEDIAAN PRODUK KELUAR -->
                        <div>
                            <label for="persediaan_produk_keluar_display" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PERSEDIAAN PRODUK KELUAR</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="persediaan_produk_keluar_display" type="text" readonly
                                       value="{{ number_format($entry->harga_persediaan_produk_jadi, 0, ',', '.') }}"
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black shadow-inner" />
                            </div>
                        </div>
                    </div>



                    <div class="flex flex-col sm:flex-row gap-6">
                        <button type="submit" class="flex-1 bg-gradient-to-b from-[#7a0e14] to-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#d4af37] shadow-xl hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all uppercase tracking-widest text-center">
                            Update Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jumlahPackManual = document.getElementById('jumlah_pack');
            const hargaPersediaanDisplay = document.getElementById('harga_persediaan_produk_jadi_display');
            const hargaPersediaanInput = document.getElementById('harga_persediaan_produk_jadi');
            const hargaPokokDisplay = document.getElementById('harga_pokok_per_pack_display');
            const hargaPokokInput = document.getElementById('harga_pokok_per_pack');
            const totalHargaDisplay = document.getElementById('total_harga_display');
            const totalHargaInput = document.getElementById('total_harga');

            function updateHargaPokok() {
                const totalInventory = parseFloat(hargaPersediaanInput.value) || 0;
                const qtyPack = parseInt(jumlahPackManual.value) || 0;
                
                if (qtyPack > 0) {
                    const costPerPack = totalInventory / qtyPack;
                    hargaPokokInput.value = costPerPack.toFixed(2);
                    hargaPokokDisplay.value = parseFloat(costPerPack.toFixed(2)).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                } else {
                    hargaPokokInput.value = 0;
                    hargaPokokDisplay.value = '0,00';
                }
                updatePersediaanKeluar();
            }

            function updatePersediaanKeluar() {
                const qtyKeluar = document.getElementById('jumlah_keluar');
                const qtyValue = parseInt(qtyKeluar.value) || 0;
                const costPerPack = parseFloat(hargaPokokInput.value) || 0;
                const totalPersediaanKeluar = qtyValue * costPerPack;
                
                const persediaanKeluarDisplay = document.getElementById('persediaan_produk_keluar_display');
                persediaanKeluarDisplay.value = totalPersediaanKeluar.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function formatCurrencyInput(input, hidden) {
                input.addEventListener('input', function() {
                    const value = this.value.replace(/[^0-9]/g, '');
                    hidden.value = value;
                    if (value) {
                        this.value = parseInt(value).toLocaleString('id-ID');
                    }
                    if (input === hargaPersediaanDisplay) updateHargaPokok();
                });
            }

            formatCurrencyInput(hargaPersediaanDisplay, hargaPersediaanInput);
            formatCurrencyInput(totalHargaDisplay, totalHargaInput);
            
            jumlahPackManual.addEventListener('input', updateHargaPokok);
            document.getElementById('jumlah_keluar').addEventListener('input', updatePersediaanKeluar);
            
            // Initial calculation
            updatePersediaanKeluar();
        });
    </script>
</x-app-layout>
