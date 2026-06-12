<x-app-layout>
    <x-slot name="header">
        Tambah Entry Produk Masuk
    </x-slot>

    <div class="py-12 min-h-screen bg-[url('/images/background_fluid.png')] bg-cover bg-center bg-fixed relative">
        <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>
        
        <div class="w-full max-w-4xl mx-auto px-6 relative z-10 space-y-8">
            <!-- Glassmorphism Card for Form -->
            <div class="bg-white/95 backdrop-blur-md border-2 border-[#d4af37]/60 rounded-[40px] shadow-[0_30px_80px_rgba(0,0,0,0.4)] p-10">
                <div class="mb-10 text-center border-b border-[#d4af37]/20 pb-8">
                    <h2 class="text-3xl font-serif font-black text-[#7a0e14] drop-shadow-sm uppercase tracking-widest">Tambah Entry Produk Masuk</h2>
                    <p class="text-gray-500 text-xs font-bold mt-2 tracking-[0.2em] uppercase">Pencatatan stok masuk ke toko</p>
                </div>

                <form method="POST" action="{{ route('persediaan.store') }}" class="space-y-8">
                    @csrf
                    
                    <input type="hidden" name="id_transaksi" value="TXN-{{ time() }}">
                    <input type="hidden" name="nama_produk" id="nama_produk_hidden" value="">
                    <input type="hidden" name="harga" id="harga_hidden" value="">
                    <input type="hidden" name="total_harga" id="total_harga_hidden" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- PRODUK ID -->
                        <div>
                            <label for="kode_produk" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">PRODUK ID <span class="text-red-500 font-black">*</span></label>
                            <div class="relative group">
                                <select id="kode_produk" name="kode_produk" 
                                        class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all appearance-none @error('kode_produk') border-red-400 @enderror" 
                                        required autofocus>
                                    <option value="">Pilih Produk ID</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->kode_produk }}" 
                                                data-nama="{{ $product->nama_produk }}" 
                                                data-harga="{{ $product->harga_fefo }}" 
                                                data-stok="{{ $product->jumlah }}">
                                            {{ $product->kode_produk }} - {{ $product->nama_produk }} ({{ $product->kategori }})
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
                                   placeholder="Input Manual No Batch" required />
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
                        <!-- JUMLAH MASUK -->
                        <div>
                            <label for="jumlah_masuk" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">JUMLAH MASUK <span class="text-red-500 font-black">*</span></label>
                            <input id="jumlah_masuk" type="number" name="jumlah_masuk" value="{{ old('jumlah_masuk') }}" 
                                   class="w-full bg-[#fdf9eb] border-2 border-[#d4af37]/30 rounded-2xl px-5 py-4 text-gray-700 font-bold focus:ring-4 focus:ring-[#d4af37]/20 focus:border-[#d4af37] transition-all placeholder-gray-400" 
                                   min="1" required />
                            <x-input-error :messages="$errors->get('jumlah_masuk')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- HARGA PER PCS -->
                        <div>
                            <label for="harga_display" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">HARGA PER PCS <span class="text-red-500 font-black">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <span class="text-[#d4af37] font-bold">Rp</span>
                                </div>
                                <input id="harga_display" type="text" readonly 
                                       class="w-full bg-[#f4ebd0] border-2 border-[#d4af37]/20 rounded-2xl pl-12 pr-5 py-4 text-[#7a0e14] font-black text-xl shadow-inner" />
                            </div>
                        </div>

                        <!-- TOTAL HARGA -->
                        <div>
                            <label for="total_harga_display" class="block text-sm font-bold text-[#7a0e14] mb-3 uppercase tracking-wider">TOTAL HARGA <span class="text-red-500 font-black">*</span></label>
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
                        <a href="{{ route('persediaan.index') }}" class="flex-1 bg-gray-500 text-white font-bold py-6 rounded-2xl shadow-xl hover:bg-gray-600 transition-all flex items-center justify-center uppercase tracking-widest text-sm text-center">
                            Batal
                        </a>
                        <button type="submit" 
                                class="flex-[2] bg-gradient-to-b from-[#7a0e14] to-[#4a080c] text-[#d4af37] font-black py-6 rounded-2xl border-2 border-[#d4af37] shadow-xl hover:from-[#8b1117] hover:to-[#5c0a0f] transition-all flex flex-col items-center justify-center gap-1 uppercase tracking-widest text-sm text-center min-h-[80px]">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Entry Masuk</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const idProdukSelect = document.getElementById('kode_produk');
                const namaProdukHidden = document.getElementById('nama_produk_hidden');
                const hargaHidden = document.getElementById('harga_hidden');
                const totalHargaHidden = document.getElementById('total_harga_hidden');
                const hargaInput = document.getElementById('harga_display');
                const jumlahMasukInput = document.getElementById('jumlah_masuk');
                const totalHargaInput = document.getElementById('total_harga_display');
                const stokSaatIniInput = document.getElementById('stok_saat_ini');

                function calculateTotalHarga() {
                    const harga = parseFloat(hargaHidden.value) || 0;
                    const jumlah = parseInt(jumlahMasukInput.value) || 0;
                    const total = harga * jumlah;
                    
                    totalHargaHidden.value = total;
                    if (total > 0) {
                        totalHargaInput.value = total.toLocaleString('id-ID');
                    } else {
                        totalHargaInput.value = '';
                    }
                }

                idProdukSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const nama = selectedOption.getAttribute('data-nama');
                    const harga = selectedOption.getAttribute('data-harga');
                    const stok = selectedOption.getAttribute('data-stok');

                    if (nama) {
                        namaProdukHidden.value = nama;
                        hargaHidden.value = harga;
                        hargaInput.value = parseFloat(harga).toLocaleString('id-ID');
                        stokSaatIniInput.value = stok || '0';
                        calculateTotalHarga();
                    } else {
                        namaProdukHidden.value = '';
                        hargaHidden.value = '';
                        hargaInput.value = '';
                        stokSaatIniInput.value = '';
                        totalHargaHidden.value = '';
                        totalHargaInput.value = '';
                    }
                });

                jumlahMasukInput.addEventListener('input', calculateTotalHarga);
            });
        </script>
    </div>
</x-app-layout>
